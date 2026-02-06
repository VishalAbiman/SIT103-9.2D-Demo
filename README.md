# SIT103 Task 9.2D - Database Form with Embedded SQL

## Live Demo
👉 [View Live Demo](https://yourusername.github.io/SIT103-9.2D-Demo/)

## Project Overview
This project demonstrates a web form with embedded SQL for inserting patient records into the RSPCA Wildlife Hospital database.

## Features Implemented
- ✅ Form validation (client & server side)
- ✅ Patient ID format validation (RSPCA-YYYY-XXXXX)
- ✅ Condition range validation (1-5)
- ✅ SQL INSERT with error handling
- ✅ User feedback messages

## Technologies Used
- HTML5, CSS3, JavaScript
- PHP (server-side processing)
- MySQL (database)
- GitHub Pages (hosting)

## Code Structure
- `index.html` - Interactive demonstration
- `code/` - Actual PHP implementation files
  - `config.php` - Database connection
  - `index.php` - HTML form with PHP
  - `insert.php` - Validation and SQL insertion

## Validation Rules
1. Patient ID must match: `RSPCA-YYYY-XXXXX`
2. Condition must be between 1 and 5
3. Admission date cannot be in the future
4. All required fields must be filled
5. Duplicate Patient IDs are prevented

## Database Schema
```sql
CREATE TABLE PATIENT (
    PatientID VARCHAR(20) PRIMARY KEY,
    SpeciesID VARCHAR(100) NOT NULL,
    AdmissionDate DATE NOT NULL,
    ConditionOnArrival INT CHECK (1-5),
    CurrentStatus VARCHAR(50) NOT NULL
);
