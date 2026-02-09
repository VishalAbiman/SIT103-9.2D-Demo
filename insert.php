<?php
// insert.php - Process form and save to SQLite database
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=" . urlencode("Invalid request method. Please use the form."));
    exit();
}

// Get and sanitize form data
$patientId = trim($_POST['patientId'] ?? '');
$speciesId = trim($_POST['speciesId'] ?? '');
$admissionDate = $_POST['admissionDate'] ?? '';
$condition = isset($_POST['condition']) ? intval($_POST['condition']) : 0;
$currentStatus = trim($_POST['currentStatus'] ?? '');
$commonName = !empty($_POST['commonName']) ? trim($_POST['commonName']) : NULL;
$weight = !empty($_POST['weight']) ? floatval($_POST['weight']) : NULL;
$injuries = !empty($_POST['injuries']) ? trim($_POST['injuries']) : NULL;

// VALIDATION
$errors = [];

// 1. Validate Patient ID format
if (empty($patientId)) {
    $errors[] = "Patient ID is required";
} elseif (!preg_match('/^RSPCA-\d{4}-[A-Z0-9]{5}$/', $patientId)) {
    $errors[] = "Invalid Patient ID format. Must be: RSPCA-YYYY-XXXXX (e.g., RSPCA-2024-001AB)";
}

// 2. Check if Patient ID already exists
if (empty($errors)) {
    $stmt = $db->prepare("SELECT PatientID FROM PATIENT WHERE PatientID = :id");
    $stmt->bindValue(':id', $patientId, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($result->fetchArray()) {
        $errors[] = "Patient ID '$patientId' already exists in database";
    }
    $stmt->close();
}

// 3. Validate species
if (empty($speciesId)) {
    $errors[] = "Species selection is required";
} else {
    $stmt = $db->prepare("SELECT SpeciesID FROM SPECIES WHERE SpeciesID = :id");
    $stmt->bindValue(':id', $speciesId, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if (!$result->fetchArray()) {
        $errors[] = "Selected species does not exist in database";
    }
    $stmt->close();
}

// 4. Validate admission date
if (empty($admissionDate)) {
    $errors[] = "Admission date is required";
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $admissionDate)) {
    $errors[] = "Invalid date format";
} elseif ($admissionDate > date('Y-m-d')) {
    $errors[] = "Admission date cannot be in the future";
}

// 5. Validate condition
if ($condition < 1 || $condition > 5) {
    $errors[] = "Condition must be between 1 and 5";
}

// 6. Validate status
if (empty($currentStatus)) {
    $errors[] = "Current status is required";
} else {
    $validStatuses = ['Critical', 'Stable', 'Rehabilitating', 'Ready for Release'];
    if (!in_array($currentStatus, $validStatuses)) {
        $errors[] = "Invalid status selected";
    }
}

// 7. Validate weight if provided
if ($weight !== NULL && ($weight <= 0 || $weight > 1000000)) {
    $errors[] = "Weight must be between 0 and 1,000,000 grams";
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    $errorMsg = urlencode(implode(' | ', $errors));
    header("Location: index.php?error=" . $errorMsg);
    exit();
}

// ALL VALIDATION PASSED - INSERT INTO DATABASE
$sql = "INSERT INTO PATIENT (
    PatientID, 
    SpeciesID, 
    AdmissionDate, 
    ConditionOnArrival, 
    CurrentStatus, 
    CommonName, 
    Weight, 
    Injuries
) VALUES (
    :patientId, 
    :speciesId, 
    :admissionDate, 
    :condition, 
    :currentStatus, 
    :commonName, 
    :weight, 
    :injuries
)";

$stmt = $db->prepare($sql);

// Bind parameters
$stmt->bindValue(':patientId', $patientId, SQLITE3_TEXT);
$stmt->bindValue(':speciesId', $speciesId, SQLITE3_TEXT);
$stmt->bindValue(':admissionDate', $admissionDate, SQLITE3_TEXT);
$stmt->bindValue(':condition', $condition, SQLITE3_INTEGER);
$stmt->bindValue(':currentStatus', $currentStatus, SQLITE3_TEXT);
$stmt->bindValue(':commonName', $commonName, SQLITE3_TEXT);
$stmt->bindValue(':weight', $weight, SQLITE3_FLOAT);
$stmt->bindValue(':injuries', $injuries, SQLITE3_TEXT);

// Execute the statement
if ($stmt->execute()) {
    // SUCCESS - Record inserted
    header("Location: index.php?success=1&id=" . urlencode($patientId));
} else {
    // DATABASE ERROR
    $error = $db->lastErrorMsg();
    
    // Format error message
    $errorMsg = "Database error";
    if (strpos($error, 'UNIQUE constraint failed') !== false) {
        $errorMsg = "Patient ID already exists";
    } elseif (strpos($error, 'CHECK constraint failed') !== false) {
        if (strpos($error, 'ConditionOnArrival') !== false) {
            $errorMsg = "Condition must be between 1 and 5";
        } elseif (strpos($error, 'CurrentStatus') !== false) {
            $errorMsg = "Invalid status value";
        }
    }
    
    header("Location: index.php?error=" . urlencode($errorMsg));
}

$stmt->close();
$db->close();
?>
