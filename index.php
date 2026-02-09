<?php
// index.php - Patient Form
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Patient - RSPCA Wildlife Hospital</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(90deg, #2c3e50, #4a6491);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 1rem;
        }
        
        .required::after {
            content: " *";
            color: #e74c3c;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f9f9f9;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn {
            background: linear-gradient(90deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid transparent;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .info-box {
            background: #e8f4fc;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 25px 0;
            border-radius: 5px;
        }
        
        .database-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            border: 1px solid #dee2e6;
        }
        
        .database-info h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .container {
                border-radius: 10px;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 RSPCA Wildlife Hospital</h1>
            <p>Patient Admission System</p>
        </div>
        
        <div class="form-container">
            <?php
            // Display success/error messages from URL parameters
            if (isset($_GET['success'])) {
                echo '<div class="message success">';
                echo '✅ Patient <strong>' . htmlspecialchars($_GET['id']) . '</strong> successfully added to database!';
                echo '</div>';
            }
            if (isset($_GET['error'])) {
                echo '<div class="message error">';
                echo '❌ Error: ' . htmlspecialchars($_GET['error']);
                echo '</div>';
            }
            ?>
            
            <div class="info-box">
                <strong>ℹ️ Form Instructions:</strong> All fields marked with * are required. 
                Patient ID must follow format: <code>RSPCA-YYYY-XXXXX</code>
            </div>
            
            <form method="POST" action="insert.php" id="patientForm">
                <!-- Patient ID -->
                <div class="form-group">
                    <label for="patientId" class="required">Patient ID</label>
                    <input type="text" id="patientId" name="patientId" required
                           placeholder="RSPCA-2024-001"
                           pattern="RSPCA-\d{4}-[A-Z0-9]{5}"
                           title="Format: RSPCA-YYYY-XXXXX (e.g., RSPCA-2024-001AB)">
                    <small style="color: #666; font-size: 0.9rem;">Example: RSPCA-2024-001AB</small>
                </div>
                
                <!-- Species -->
                <div class="form-group">
                    <label for="speciesId" class="required">Species</label>
                    <select id="speciesId" name="speciesId" required>
                        <option value="">-- Select a Species --</option>
                        <?php
                        // Get species from database
                        $result = $db->query("SELECT SpeciesID, CommonName FROM SPECIES ORDER BY CommonName");
                        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                            $selected = ($_GET['species'] ?? '') === $row['SpeciesID'] ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($row['SpeciesID']) . '" ' . $selected . '>';
                            echo htmlspecialchars($row['CommonName']);
                            echo '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <!-- Admission Date -->
                <div class="form-group">
                    <label for="admissionDate" class="required">Admission Date</label>
                    <input type="date" id="admissionDate" name="admissionDate" required>
                </div>
                
                <!-- Condition -->
                <div class="form-group">
                    <label for="condition" class="required">Condition on Arrival (1-5)</label>
                    <input type="number" id="condition" name="condition" min="1" max="5" required
                           placeholder="3">
                    <small style="color: #666; font-size: 0.9rem;">1 = Critical, 5 = Excellent</small>
                </div>
                
                <!-- Current Status -->
                <div class="form-group">
                    <label for="currentStatus" class="required">Current Status</label>
                    <select id="currentStatus" name="currentStatus" required>
                        <option value="">-- Select Status --</option>
                        <option value="Critical">Critical</option>
                        <option value="Stable">Stable</option>
                        <option value="Rehabilitating">Rehabilitating</option>
                        <option value="Ready for Release">Ready for Release</option>
                    </select>
                </div>
                
                <!-- Common Name -->
                <div class="form-group">
                    <label for="commonName">Common Name (Optional)</label>
                    <input type="text" id="commonName" name="commonName" 
                           placeholder="e.g., Cockatoo, Wombat, Python">
                </div>
                
                <!-- Weight -->
                <div class="form-group">
                    <label for="weight">Weight in grams (Optional)</label>
                    <input type="number" id="weight" name="weight" step="0.01"
                           placeholder="e.g., 820.50">
                </div>
                
                <!-- Injuries -->
                <div class="form-group">
                    <label for="injuries">Injuries & Observations (Optional)</label>
                    <textarea id="injuries" name="injuries" 
                              placeholder="Describe injuries, symptoms, or observations..."></textarea>
                </div>
                
                <button type="submit" class="btn">
                    📋 Add Patient Record
                </button>
            </form>
            
            <div class="database-info">
                <h3>📊 Database Information</h3>
                <?php
                // Get patient count
                $patientCount = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT");
                $speciesCount = $db->querySingle("SELECT COUNT(*) as count FROM SPECIES");
                
                echo "<p><strong>Total Patients in Database:</strong> $patientCount</p>";
                echo "<p><strong>Available Species:</strong> $speciesCount</p>";
                
                // Show recent patients
                $recentResult = $db->query("SELECT PatientID, AdmissionDate FROM PATIENT ORDER BY CreatedAt DESC LIMIT 3");
                if ($recentResult) {
                    echo "<p><strong>Recent Patients:</strong></p>";
                    echo "<ul style='margin-left: 20px;'>";
                    while ($row = $recentResult->fetchArray(SQLITE3_ASSOC)) {
                        echo "<li>" . htmlspecialchars($row['PatientID']) . " (Admitted: " . htmlspecialchars($row['AdmissionDate']) . ")</li>";
                    }
                    echo "</ul>";
                }
                ?>
                <p><a href="viewpatients.php" style="color: #3498db; text-decoration: none; font-weight: 600;">
                    👁️ View All Patients
                </a></p>
            </div>
        </div>
    </div>
    
    <script>
        // Set max date to today
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('admissionDate');
            const today = new Date().toISOString().split('T')[0];
            dateInput.max = today;
            
            // Set default to today
            dateInput.value = today;
            
            // Auto-suggest Patient ID based on year
            dateInput.addEventListener('change', function() {
                const year = this.value.substring(0, 4);
                const patientIdInput = document.getElementById('patientId');
                if (!patientIdInput.value) {
                    patientIdInput.value = 'RSPCA-' + year + '-001';
                }
            });
            
            // Real-time Patient ID validation
            const patientIdInput = document.getElementById('patientId');
            patientIdInput.addEventListener('input', function() {
                const pattern = /^RSPCA-\d{4}-[A-Z0-9]{5}$/;
                if (this.value && !pattern.test(this.value)) {
                    this.style.borderColor = '#e74c3c';
                    this.style.backgroundColor = '#ffeaea';
                } else {
                    this.style.borderColor = '#ddd';
                    this.style.backgroundColor = '#f9f9f9';
                }
            });
            
            // Real-time condition validation
            const conditionInput = document.getElementById('condition');
            conditionInput.addEventListener('input', function() {
                if (this.value < 1 || this.value > 5) {
                    this.style.borderColor = '#e74c3c';
                    this.style.backgroundColor = '#ffeaea';
                } else {
                    this.style.borderColor = '#ddd';
                    this.style.backgroundColor = '#f9f9f9';
                }
            });
        });
    </script>
</body>
</html>
