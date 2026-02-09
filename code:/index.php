<?php
// index.php - Patient Admission Form for RSPCA
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection from parent folder
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSPCA Wildlife Hospital - Patient Admission</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(90deg, var(--primary-color), #4a6491);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-top: 10px;
        }
        
        .badge {
            background: var(--secondary-color);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .form-container {
            padding: 40px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid transparent;
            animation: fadeIn 0.5s ease-in;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: var(--success-color);
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: var(--danger-color);
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: var(--secondary-color);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1rem;
        }
        
        .required::after {
            content: " *";
            color: var(--danger-color);
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f9f9f9;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--secondary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }
        
        .btn-submit {
            background: linear-gradient(90deg, var(--success-color), #2ecc71);
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 30px auto;
            min-width: 250px;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(46, 204, 113, 0.3);
        }
        
        .btn-submit:active {
            transform: translateY(-1px);
        }
        
        .stats-panel {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-top: 40px;
            border: 1px solid #dee2e6;
        }
        
        .stats-panel h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .action-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .action-link {
            padding: 10px 20px;
            background: var(--secondary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .action-link:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .action-link.view {
            background: #9b59b6;
        }
        
        .action-link.view:hover {
            background: #8e44ad;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 25px;
            }
            
            header {
                padding: 20px;
            }
            
            header h1 {
                font-size: 2rem;
                flex-direction: column;
                gap: 10px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-submit {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>
                <span>🏥 RSPCA Wildlife Hospital</span>
                <span class="badge">Patient Admission System</span>
            </h1>
            <p>Task 9.2D - Database Form with Embedded SQL</p>
        </header>
        
        <div class="form-container">
            <?php
            // Display success/error messages from URL parameters
            if (isset($_GET['success'])) {
                $patientId = htmlspecialchars($_GET['id']);
                echo '<div class="alert alert-success">';
                echo '<strong>✅ Success!</strong> Patient record <code>' . $patientId . '</code> has been added to the database.';
                echo '</div>';
            }
            
            if (isset($_GET['error'])) {
                $errorMsg = htmlspecialchars($_GET['error']);
                echo '<div class="alert alert-error">';
                echo '<strong>❌ Error:</strong> ' . $errorMsg;
                echo '</div>';
            }
            ?>
            
            <div class="alert alert-info">
                <strong>ℹ️ Form Instructions:</strong> All fields marked with <span class="required">*</span> are required. 
                Patient ID must follow format: <code>RSPCA-YYYY-XXXXX</code> (e.g., RSPCA-2024-001AB)
            </div>
            
            <form method="POST" action="insert.php" id="patientForm">
                <div class="form-grid">
                    <!-- Patient ID -->
                    <div class="form-group">
                        <label for="patientId" class="required">Patient ID</label>
                        <input type="text" id="patientId" name="patientId" required
                               placeholder="RSPCA-2024-001"
                               pattern="RSPCA-\d{4}-[A-Z0-9]{5}"
                               title="Format: RSPCA-YYYY-XXXXX (e.g., RSPCA-2024-001AB)">
                        <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                            Unique identifier for the patient
                        </small>
                    </div>
                    
                    <!-- Species -->
                    <div class="form-group">
                        <label for="speciesId" class="required">Species</label>
                        <select id="speciesId" name="speciesId" required>
                            <option value="">-- Select Species --</option>
                            <?php
                            // Get species from database
                            $result = $db->query("SELECT SpeciesID, CommonName FROM SPECIES ORDER BY CommonName");
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                                echo '<option value="' . htmlspecialchars($row['SpeciesID']) . '">';
                                echo htmlspecialchars($row['CommonName']);
                                echo '</option>';
                            }
                            ?>
                        </select>
                        <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                            Select the animal species
                        </small>
                    </div>
                    
                    <!-- Admission Date -->
                    <div class="form-group">
                        <label for="admissionDate" class="required">Admission Date</label>
                        <input type="date" id="admissionDate" name="admissionDate" required>
                        <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                            Date when the patient was admitted
                        </small>
                    </div>
                    
                    <!-- Condition -->
                    <div class="form-group">
                        <label for="condition" class="required">Condition on Arrival</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="range" id="conditionRange" name="condition" min="1" max="5" value="3" 
                                   style="flex: 1;" oninput="updateConditionValue(this.value)">
                            <span id="conditionValue" style="font-weight: bold; min-width: 30px;">3</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 5px; font-size: 0.85rem; color: #666;">
                            <span>1 (Critical)</span>
                            <span>2</span>
                            <span>3</span>
                            <span>4</span>
                            <span>5 (Excellent)</span>
                        </div>
                        <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                            Patient's condition scale from 1 (worst) to 5 (best)
                        </small>
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
                        <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                            Current medical status of the patient
                        </small>
                    </div>
                    
                    <!-- Common Name -->
                    <div class="form-group">
                        <label for="commonName">Common Name (Optional)</label>
                        <input type="text" id="commonName" name="commonName" 
                               placeholder="e.g., Fred the Cockatoo">
                        <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                            Nickname or common name for the animal
                        </small>
                    </div>
                    
                    <!-- Weight -->
                    <div class="form-group">
                        <label for="weight">Weight in grams (Optional)</label>
                        <input type="number" id="weight" name="weight" step="0.01" min="0"
                               placeholder="e.g., 820.50">
                        <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                            Weight in grams (1 kg = 1000 g)
                        </small>
                    </div>
                </div>
                
                <!-- Injuries -->
                <div class="form-group">
                    <label for="injuries">Injuries & Observations (Optional)</label>
                    <textarea id="injuries" name="injuries" 
                              placeholder="Describe injuries, symptoms, treatment given, or any observations..."></textarea>
                    <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                        Detailed notes about the patient's condition
                    </small>
                </div>
                
                <button type="submit" class="btn-submit">
                    <span>📋 Add Patient Record</span>
                    <span>→</span>
                </button>
            </form>
            
            <div class="stats-panel">
                <h3>📊 Database Statistics</h3>
                
                <div class="stats-grid">
                    <?php
                    // Get statistics
                    $totalPatients = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT");
                    $criticalCount = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT WHERE CurrentStatus = 'Critical'");
                    $stableCount = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT WHERE CurrentStatus = 'Stable'");
                    $speciesCount = $db->querySingle("SELECT COUNT(*) as count FROM SPECIES");
                    ?>
                    
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $totalPatients; ?></div>
                        <div class="stat-label">Total Patients</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $criticalCount; ?></div>
                        <div class="stat-label">Critical Cases</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $stableCount; ?></div>
                        <div class="stat-label">Stable Patients</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $speciesCount; ?></div>
                        <div class="stat-label">Species Types</div>
                    </div>
                </div>
                
                <div class="action-links">
                    <a href="../viewpatients.php" class="action-link view">
                        <span>👁️ View All Patients</span>
                    </a>
                    <a href="#database-info" class="action-link" onclick="showDatabaseInfo()">
                        <span>💾 Database Info</span>
                    </a>
                </div>
                
                <div id="database-info" style="display: none; margin-top: 20px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #dee2e6;">
                    <h4>Database Information</h4>
                    <p><strong>File:</strong> rspca.db (SQLite3)</p>
                    <p><strong>Location:</strong> Main project folder</p>
                    <p><strong>Size:</strong> 
                        <?php 
                        if (file_exists('../rspca.db')) {
                            echo round(filesize('../rspca.db') / 1024, 2) . ' KB';
                        } else {
                            echo 'File not created yet';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Set max date to today and default to today
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('admissionDate');
            const today = new Date().toISOString().split('T')[0];
            dateInput.max = today;
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
                    this.style.borderColor = '#e0e0e0';
                    this.style.backgroundColor = '#f9f9f9';
                }
            });
        });
        
        // Update condition value display
        function updateConditionValue(value) {
            document.getElementById('conditionValue').textContent = value;
        }
        
        // Show database info
        function showDatabaseInfo() {
            const infoDiv = document.getElementById('database-info');
            infoDiv.style.display = infoDiv.style.display === 'none' ? 'block' : 'none';
        }
        
        // Form validation before submit
        document.getElementById('patientForm').addEventListener('submit', function(e) {
            const patientId = document.getElementById('patientId').value;
            const pattern = /^RSPCA-\d{4}-[A-Z0-9]{5}$/;
            
            if (!pattern.test(patientId)) {
                e.preventDefault();
                alert('❌ Invalid Patient ID format!\n\nPlease use: RSPCA-YYYY-XXXXX\nExample: RSPCA-2024-001AB');
                document.getElementById('patientId').focus();
            }
        });
    </script>
</body>
</html>
