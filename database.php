<?php
// database.php - SQLite version
$db = new SQLite3('rspca.db');

// Create tables
$db->exec("CREATE TABLE IF NOT EXISTS PATIENT (
    PatientID TEXT PRIMARY KEY,
    SpeciesID TEXT NOT NULL,
    AdmissionDate TEXT NOT NULL,
    ConditionOnArrival INTEGER CHECK (ConditionOnArrival BETWEEN 1 AND 5),
    CurrentStatus TEXT NOT NULL,
    CommonName TEXT,
    Weight REAL,
    Injuries TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS SPECIES (
    SpeciesID TEXT PRIMARY KEY,
    CommonName TEXT
)");

// Insert sample species
$db->exec("INSERT OR IGNORE INTO SPECIES VALUES 
    ('Vombatus_ursinus', 'Common Wombat'),
    ('Cacatua_galerita', 'Sulphur-crested Cockatoo')");
?>
