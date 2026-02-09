<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection (SQLite version)
if (!file_exists('rspca.db')) {
    // Create database file if it doesn't exist
    touch('rspca.db');
}

$db = new SQLite3('rspca.db');

// Create tables if they don't exist
$db->exec("CREATE TABLE IF NOT EXISTS PATIENT (
    PatientID TEXT PRIMARY KEY,
    SpeciesID TEXT NOT NULL,
    AdmissionDate TEXT NOT NULL,
    ConditionOnArrival INTEGER CHECK (ConditionOnArrival BETWEEN 1 AND 5),
    CurrentStatus TEXT NOT NULL CHECK (CurrentStatus IN ('Critical', 'Stable', 'Rehabilitating', 'Ready for Release')),
    CommonName TEXT,
    Weight REAL,
    Injuries TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS SPECIES (
    SpeciesID TEXT PRIMARY KEY,
    CommonName TEXT NOT NULL
)");

// Insert sample species if table is empty
$result = $db->query("SELECT COUNT(*) as count FROM SPECIES");
$row = $result->fetchArray(SQLITE3_ASSOC);
if ($row['count'] == 0) {
    $db->exec("INSERT INTO SPECIES VALUES 
        ('Vombatus_ursinus', 'Common Wombat'),
        ('Cacatua_galerita', 'Sulphur-crested Cockatoo'),
        ('Trichosurus_vulpecula', 'Common Brushtail Possum'),
        ('Morelia_spilota', 'Carpet Python'),
        ('Ornithorhynchus_anatinus', 'Platypus')");
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=" . urlencode("Invalid request method. Please submit the form."));
    exit();
}

// Get form data with sanitization
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

// 1. Check Patient ID format
if (empty($patientId)) {
    $errors[] = "Patient ID is required";
} elseif (!preg_match('/^RSPCA-\d{4}-[A-Z0-9]{5}$/', $patientId)) {
    $errors[] = "Invalid Patient ID format. Must be: RSPCA-YYYY-XXXXX (e.g., RSPCA-2024-001AB)";
}

// 2. Check if Patient ID already exists
if (empty($errors)) {
    $stmt = $db->prepare("SELECT PatientID FROM PATIENT WHERE PatientID = :patientId");
    $stmt->bindValue(':patientId', $patientId, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $errors[] = "Patient ID '$patientId' already exists in database";
    }
    $stmt->close();
}

// 3. Validate species exists
if (empty($speciesId)) {
    $errors[] = "Species selection is required";
} else {
    $stmt = $db->prepare("SELECT SpeciesID FROM SPECIES WHERE SpeciesID = :speciesId");
    $stmt->bindValue(':speciesId', $speciesId, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if (!$result->fetchArray(SQLITE3_ASSOC)) {
        $errors[] = "Selected species does not exist in database";
    }
    $stmt->close();
}

// 4. Validate admission date
if (empty($admissionDate)) {
    $errors[] = "Admission date is required";
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $admissionDate)) {
    $errors[] = "Invalid date format. Use YYYY-MM-DD";
} else {
    // Check if date is valid
    $dateParts = explode('-', $admissionDate);
    if (!checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
        $errors[] = "Invalid date";
    } elseif ($admissionDate > date('Y-m-d')) {
        $errors[] = "Admission date cannot be in the future";
    }
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
        $errors[] = "Invalid status selected. Must be one of: " . implode(', ', $validStatuses);
    }
}

// 7. Validate weight if provided
if ($weight !== NULL && ($weight <= 0 || $weight > 1000000)) {
    $errors[] = "Weight must be between 0 and 1,000,000 grams";
}

// If errors, redirect back with error
if (!empty($errors)) {
    $errorMsg = urlencode(implode(' | ', $errors));
    header("Location: index.php?error=" . $errorMsg);
    exit();
}

// INSERT INTO DATABASE using prepared statement
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

// Execute and check result
if ($stmt->execute()) {
    // Success - get the last inserted row
    $lastId = $db->lastInsertRowID();
    
    // Log the insertion (optional)
    error_log("Patient inserted: $patientId at " . date('Y-m-d H:i:s'));
    
    // Redirect with success message
    header("Location: index.php?success=1&id=" . urlencode($patientId));
} else {
    // Get SQLite error
    $error = $db->lastErrorMsg();
    
    // Check for specific constraint violations
    if (strpos($error, 'CHECK constraint failed: ConditionOnArrival') !== false) {
        $errorMsg = "Condition must be between 1 and 5";
    } elseif (strpos($error, 'CHECK constraint failed: CurrentStatus') !== false) {
        $errorMsg = "Invalid status value. Must be: Critical, Stable, Rehabilitating, or Ready for Release";
    } elseif (strpos($error, 'UNIQUE constraint failed') !== false) {
        $errorMsg = "Patient ID '$patientId' already exists";
    } else {
        $errorMsg = "Database error: " . htmlspecialchars($error);
    }
    
    header("Location: index.php?error=" . urlencode($errorMsg));
}

$stmt->close();
$db->close();
?>
