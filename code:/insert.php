<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=" . urlencode("Invalid request method"));
    exit();
}

// Get form data with sanitization
$patientId = trim($_POST['patientId'] ?? '');
$speciesId = trim($_POST['speciesId'] ?? '');
$admissionDate = $_POST['admissionDate'] ?? '';
$condition = intval($_POST['condition'] ?? 0);
$currentStatus = trim($_POST['currentStatus'] ?? '');
$commonName = !empty($_POST['commonName']) ? trim($_POST['commonName']) : NULL;
$weight = !empty($_POST['weight']) ? floatval($_POST['weight']) : NULL;
$injuries = !empty($_POST['injuries']) ? trim($_POST['injuries']) : NULL;

// VALIDATION
$errors = [];

// 1. Check Patient ID format
if (!preg_match('/^RSPCA-\d{4}-[A-Z0-9]{5}$/', $patientId)) {
    $errors[] = "Invalid Patient ID format. Use: RSPCA-YYYY-XXXXX (e.g., RSPCA-2024-001AB)";
}

// 2. Check if Patient ID already exists
$check_stmt = $conn->prepare("SELECT PatientID FROM PATIENT WHERE PatientID = ?");
$check_stmt->bind_param("s", $patientId);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $errors[] = "Patient ID '$patientId' already exists in database";
}
$check_stmt->close();

// 3. Validate species exists (optional but good practice)
$species_stmt = $conn->prepare("SELECT SpeciesID FROM SPECIES WHERE SpeciesID = ?");
$species_stmt->bind_param("s", $speciesId);
$species_stmt->execute();
$species_result = $species_stmt->get_result();
if ($species_result->num_rows == 0) {
    $errors[] = "Selected species does not exist";
}
$species_stmt->close();

// 4. Validate condition
if ($condition < 1 || $condition > 5) {
    $errors[] = "Condition must be between 1 and 5";
}

// 5. Validate date (not future)
$today = date('Y-m-d');
if ($admissionDate > $today) {
    $errors[] = "Admission date cannot be in the future";
}

// 6. Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $admissionDate)) {
    $errors[] = "Invalid date format";
}

// 7. Validate status
$validStatuses = ['Critical', 'Stable', 'Rehabilitating', 'Ready for Release'];
if (!in_array($currentStatus, $validStatuses)) {
    $errors[] = "Invalid status selected";
}

// If errors, redirect back
if (!empty($errors)) {
    $errorMsg = urlencode(implode(' | ', $errors));
    header("Location: index.php?error=" . $errorMsg);
    exit();
}

// INSERT INTO DATABASE using prepared statement (prevents SQL injection)
$sql = "INSERT INTO PATIENT (PatientID, SpeciesID, AdmissionDate, ConditionOnArrival, 
        CurrentStatus, CommonName, Weight, Injuries) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: index.php?error=" . urlencode("Database error preparing statement"));
    exit();
}

// Bind parameters
$stmt->bind_param("sssissss", 
    $patientId, 
    $speciesId, 
    $admissionDate, 
    $condition,
    $currentStatus,
    $commonName,
    $weight,
    $injuries
);

// Execute and check result
if ($stmt->execute()) {
    // Success
    header("Location: index.php?success=1&id=" . urlencode($patientId));
} else {
    // Check for specific constraint violations
    $error = $stmt->error;
    
    if (strpos($error, 'ConditionOnArrival') !== false) {
        $errorMsg = "Condition must be between 1 and 5";
    } elseif (strpos($error, 'CurrentStatus') !== false) {
        $errorMsg = "Invalid status value";
    } elseif (strpos($error, 'PatientID') !== false) {
        $errorMsg = "Patient ID format invalid";
    } elseif (strpos($error, 'foreign key constraint') !== false) {
        $errorMsg = "Invalid species selected";
    } else {
        $errorMsg = "Database error: " . $error;
    }
    
    header("Location: index.php?error=" . urlencode($errorMsg));
}

$stmt->close();
$conn->close();
?>