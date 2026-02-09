# RSPCA Wildlife Hospital - Patient Admission System

## 📋 Project Overview
**Task 9.2D - SIT103 Database Concepts**  
A complete web-based patient admission system for RSPCA Victoria Wildlife Hospital with embedded SQL, form validation, and database operations.

**🌐 Live Demo:** [GitHub Pages Link Here]  
**📁 Repository:** [GitHub Repository Link Here]

## 🎯 Features Implemented
- ✅ **Form Validation** - Client & server-side validation
- ✅ **Patient ID Format** - RSPCA-YYYY-XXXXX pattern matching
- ✅ **Condition Range** - 1-5 scale with validation
- ✅ **Database Operations** - SQLite database with CRUD operations
- ✅ **Error Handling** - User-friendly error/success messages
- ✅ **Data Persistence** - localStorage (demo) & SQLite (full version)
- ✅ **Export Functionality** - CSV export of patient records

## 🏗️ Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+ with SQLite3
- **Database**: SQLite (file-based, no server needed)
- **Hosting**: GitHub Pages (demo) + Local PHP server (full version)

## 📁 File Structure
```
SIT103_9.2D/
├── index.html          # Main patient form (GitHub Pages)
├── view.html           # View saved patients
├── demo.js             # JavaScript database simulation
├── style.css           # Complete styling
├── README.md           # Documentation
└── code/               # Complete PHP/SQLite implementation
    ├── index.php       # PHP patient form
    ├── insert.php      # Form processing & validation
    ├── config.php      # Database connection (SQLite)
    └── viewpatients.php # View all records
```

## 🚀 Quick Start

### Option A: GitHub Pages Demo
1. Visit: `https://[username].github.io/SIT103_9.2D/`
2. Fill form with valid data
3. Click "Save Patient"
4. View saved data

### Option B: Local Development
```bash
# Clone repository
git clone https://github.com/[username]/SIT103_9.2D.git
cd SIT103_9.2D/code

# Start PHP server
php -S localhost:8000

# Open browser: http://localhost:8000/index.php
```

## 🗄️ Database Schema
```sql
CREATE TABLE PATIENT (
    PatientID TEXT PRIMARY KEY,
    SpeciesID TEXT NOT NULL,
    AdmissionDate TEXT NOT NULL,
    ConditionOnArrival INTEGER CHECK (1-5),
    CurrentStatus TEXT CHECK ('Critical','Stable','Rehabilitating','Ready for Release'),
    CommonName TEXT,
    Weight REAL,
    Injuries TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## ✅ Validation Rules
1. **Patient ID**: `RSPCA-YYYY-XXXXX` format, unique
2. **Condition**: Integer 1-5 (1=Critical, 5=Excellent)
3. **Date**: Cannot be in future, valid format
4. **Species**: Must exist in database
5. **Status**: Must be from predefined list
6. **Weight**: Optional, 0-1,000,000 grams

## 🧪 Testing Scenarios

### Test 1: Failed Validation
- Patient ID: `TEST-123` ❌
- Condition: `10` ❌
- Result: Error messages, no database insertion

### Test 2: Successful Insertion
- Patient ID: `RSPCA-2024-010` ✅
- Species: Common Wombat ✅
- Condition: `3` ✅
- Result: Success message, saved to database

### Test 3: Duplicate Prevention
- Patient ID: `RSPCA-2024-001` (already exists) ❌
- Result: "Patient ID already exists" error

## 🔒 Security Features
- **SQL Injection Prevention**: Prepared statements
- **Input Sanitization**: Trim, type casting, escaping
- **Validation Layers**: Client + server + database constraints
- **Error Handling**: User-friendly messages (no technical details)

## 📊 Database Operations
- **INSERT**: Add new patient records with validation
- **SELECT**: View all patients with filtering
- **UPDATE**: (Future feature) Modify existing records
- **DELETE**: (Future feature) Remove records
- **EXPORT**: Download data as CSV file

## 🎥 Video Demonstration Points
1. **Form Interface** - Show all fields and validation
2. **Failed Insertion** - Demonstrate error handling
3. **Successful Insertion** - Show complete workflow
4. **Database Verification** - View saved records
5. **Code Walkthrough** - Explain validation logic and SQL

## 🔧 Troubleshooting

### Common Issues:
1. **Form not submitting**: Check JavaScript console, ensure required fields
2. **Data not saving**: Check localStorage permissions (demo) or PHP write permissions (full version)
3. **Dropdown empty**: Verify database connection and species data
4. **Validation errors**: Check Patient ID format and condition range

### Browser Compatibility:
- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 12+
- ✅ Edge 79+
- ❌ Internet Explorer

## 📝 Assessment Requirements Met
| Requirement | Status | Details |
|------------|--------|---------|
| Form to insert records | ✅ | Complete form with 7 fields |
| Embedded SQL | ✅ | SQL INSERT in PHP/SQLite |
| Validation | ✅ | Client & server side |
| Error messages | ✅ | User-friendly errors |
| Success confirmation | ✅ | Green success messages |
| Database connection | ✅ | SQLite working |
| Video demonstration | ✅ | All features ready |

## 📞 Support
- **Student**: Vishal Sanketh Abiman
- **Student ID**: S224373871
- **Unit**: SIT103 Database Concepts
- **Date**: February 2026

For issues:
1. Check browser console (F12)
2. Clear cache and cookies
3. Try different browser
4. Contact via student email

## 📄 License
© 2026 Vishal Sanketh Abiman (S224373871)  
Created for SIT103 Database Concepts - Educational Use Only

---

**✅ Project Complete - Ready for Assessment**
