<?php
// config.php - SQLite Database Connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// SQLite database file
$dbFile = 'rspca.db';

// Check if database file exists, create if not
if (!file_exists($dbFile)) {
    touch($dbFile);
    chmod($dbFile, 0644);
    echo "<!-- Database file created -->";
}

// Connect to SQLite database
try {
    $db = new SQLite3($dbFile, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    $db->busyTimeout(5000);
    $db->exec('PRAGMA foreign_keys = ON');
    $db->exec('PRAGMA encoding = "UTF-8"');
    
} catch (Exception $e) {
    die("<div style='color:red; padding:20px;'>
        <h3>Database Connection Error</h3>
        <p>" . $e->getMessage() . "</p>
        <p>Make sure SQLite is enabled in PHP.</p>
        </div>");
}

// Create PATIENT table if it doesn't exist
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

// Create SPECIES table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS SPECIES (
    SpeciesID TEXT PRIMARY KEY,
    CommonName TEXT NOT NULL
)");

// Insert sample species if table is empty
$result = $db->querySingle("SELECT COUNT(*) as count FROM SPECIES");
if ($result == 0) {
    $db->exec("INSERT INTO SPECIES VALUES 
        ('Vombatus_ursinus', 'Common Wombat'),
        ('Cacatua_galerita', 'Sulphur-crested Cockatoo'),
        ('Trichosurus_vulpecula', 'Common Brushtail Possum'),
        ('Morelia_spilota', 'Carpet Python'),
        ('Ornithorhynchus_anatinus', 'Platypus')");
}

// For backward compatibility with existing code
$conn = $db;
?>
