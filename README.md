# SIT103 Task 9.2D - Database Form with Embedded SQL

## Project Overview
A web-based patient admission system for RSPCA Wildlife Hospital with form validation and SQLite database storage.

## Features
- ✅ Form with client-side validation
- ✅ Server-side validation in PHP
- ✅ SQLite database storage
- ✅ Patient ID format validation (RSPCA-YYYY-XXXXX)
- ✅ Condition range validation (1-5)
- ✅ Date validation (not in future)
- ✅ Duplicate prevention
- ✅ View all saved records

## Files
1. `index.php` - Patient admission form
2. `insert.php` - Form processing and database insertion
3. `config.php` - SQLite database connection
4. `viewpatients.php` - View all saved patients
5. `rspca.db` - SQLite database file (auto-created)

## Setup Instructions
1. Place all files in a web-accessible directory
2. Ensure PHP has SQLite support
3. Run: `php -S localhost:8000`
4. Open: `http://localhost:8000/index.php`

## Database Schema
```sql
-- PATIENT table
CREATE TABLE PATIENT (
    PatientID TEXT PRIMARY KEY,
    SpeciesID TEXT NOT NULL,
    AdmissionDate TEXT NOT NULL,
    ConditionOnArrival INTEGER CHECK (1-5),
    CurrentStatus TEXT CHECK (Critical, Stable, Rehabilitating, Ready for Release),
    CommonName TEXT,
    Weight REAL,
    Injuries TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SPECIES table
CREATE TABLE SPECIES (
    SpeciesID TEXT PRIMARY KEY,
    CommonName TEXT NOT NULL
);
