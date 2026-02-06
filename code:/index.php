<!DOCTYPE html>
<html>
<head>
    <title>Add Patient - RSPCA</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f0f0; }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 800px; margin: auto; }
        h2 { color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .required { color: red; }
        .btn { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #219653; }
        .error { color: red; margin-top: 5px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📋 Add New Patient to RSPCA Database</h2>
        
        <?php
        // Add error reporting at the top
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Display success/error messages
        if (isset($_GET['success'])) {
            echo '<div class="success">✅ Patient added successfully! ID: ' . htmlspecialchars($_GET['id']) . '</div><br>';
        }
        if (isset($_GET['error'])) {
            echo '<div class="error">❌ Error: ' . htmlspecialchars($_GET['error']) . '</div><br>';
        }
        ?>
        
        <form method="POST" action="insert.php">
            <!-- Required Fields -->
            <div class="form-group">
                <label>Patient ID <span class="required">*</span></label>
                <input type="text" name="patientId" required 
                       placeholder="RSPCA-2024-001" 
                       pattern="RSPCA-\d{4}-[A-Z0-9]{5}"
                       title="Format: RSPCA-YYYY-XXXXX (e.g., RSPCA-2024-001AB)">
            </div>
            
            <div class="form-group">
                <label>Species <span class="required">*</span></label>
                <select name="speciesId" required>
                    <option value="">Select Species</option>
                    <?php
                    include 'config.php';
                    // Check if connection worked before querying
                    if ($conn) {
                        $result = $conn->query("SELECT SpeciesID, CommonName FROM SPECIES");
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="'.$row['SpeciesID'].'">'.$row['CommonName'].'</option>';
                            }
                        } else {
                            echo '<option value="">Error loading species</option>';
                        }
                    } else {
                        echo '<option value="">Database not connected</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Admission Date <span class="required">*</span></label>
                <input type="date" name="admissionDate" required>
            </div>
            
            <div class="form-group">
                <label>Condition (1-5) <span class="required">*</span></label>
                <input type="number" name="condition" min="1" max="5" required>
                <small>1=Critical, 5=Excellent</small>
            </div>
            
            <div class="form-group">
                <label>Current Status <span class="required">*</span></label>
                <select name="currentStatus" required>
                    <option value="">Select Status</option>
                    <option value="Critical">Critical</option>
                    <option value="Stable">Stable</option>
                    <option value="Rehabilitating">Rehabilitating</option>
                    <option value="Ready for Release">Ready for Release</option>
                </select>
            </div>
            
            <!-- Optional Fields -->
            <div class="form-group">
                <label>Common Name</label>
                <input type="text" name="commonName" placeholder="e.g., Cockatoo">
            </div>
            
            <div class="form-group">
                <label>Weight (grams)</label>
                <input type="number" step="0.01" name="weight" placeholder="e.g., 820.50">
            </div>
            
            <div class="form-group">
                <label>Injuries/Observations</label>
                <textarea name="injuries" rows="3" placeholder="Describe injuries..."></textarea>
            </div>
            
            <button type="submit" class="btn">➕ Add Patient Record</button>
        </form>
        
        <hr>
        <h3>📊 Quick Database Info</h3>
        <?php
        // Don't close connection yet - we already included config.php above
        if (isset($conn) && $conn) {
            $result = $conn->query("SELECT COUNT(*) as total FROM PATIENT");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "Total patients in database: <strong>" . $row['total'] . "</strong>";
            } else {
                echo "No PATIENT table found or error querying.";
            }
        } else {
            echo "Database not connected.";
        }
        ?>
    </div>
    
    <script>
        // Set max date to today
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.querySelector('input[name="admissionDate"]');
            if (dateInput) {
                dateInput.max = new Date().toISOString().split('T')[0];
                
                // Auto-suggest Patient ID based on date
                dateInput.addEventListener('change', function() {
                    const year = this.value.substring(0,4);
                    const patientIdField = document.querySelector('input[name="patientId"]');
                    if (patientIdField && !patientIdField.value) {
                        patientIdField.value = 'RSPCA-' + year + '-001';
                    }
                });
            }
        });
    </script>
</body>
</html>