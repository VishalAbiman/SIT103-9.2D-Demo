<?php
// viewpatients.php - View all saved patients
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Patients - RSPCA</title>
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
            max-width: 1200px;
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
        
        .content {
            padding: 30px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .stat-card h3 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #666;
            font-size: 0.9rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        th {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .status-critical {
            color: #e74c3c;
            font-weight: 600;
        }
        
        .status-stable {
            color: #2ecc71;
            font-weight: 600;
        }
        
        .status-rehab {
            color: #f39c12;
            font-weight: 600;
        }
        
        .status-ready {
            color: #3498db;
            font-weight: 600;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.1rem;
        }
        
        .condition-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            min-width: 60px;
            text-align: center;
        }
        
        .condition-1 { background: #e74c3c; color: white; }
        .condition-2 { background: #e67e22; color: white; }
        .condition-3 { background: #f1c40f; color: #333; }
        .condition-4 { background: #2ecc71; color: white; }
        .condition-5 { background: #27ae60; color: white; }
        
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Patient Records</h1>
            <p>RSPCA Wildlife Hospital Database</p>
        </div>
        
        <div class="content">
            <?php
            // Get statistics
            $totalPatients = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT");
            $criticalPatients = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT WHERE CurrentStatus = 'Critical'");
            $todayPatients = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT WHERE AdmissionDate = date('now')");
            ?>
            
            <div class="stats">
                <div class="stat-card">
                    <h3><?php echo $totalPatients; ?></h3>
                    <p>Total Patients</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $criticalPatients; ?></h3>
                    <p>Critical Patients</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $todayPatients; ?></h3>
                    <p>Admitted Today</p>
                </div>
            </div>
            
            <h2>All Patient Records</h2>
            
            <?php
            // Get all patients
            $result = $db->query("
                SELECT p.*, s.CommonName as SpeciesName 
                FROM PATIENT p
                LEFT JOIN SPECIES s ON p.SpeciesID = s.SpeciesID
                ORDER BY p.AdmissionDate DESC, p.CreatedAt DESC
            ");
            
            if (!$result || $totalPatients == 0) {
                echo '<div class="no-data">
                    <p>No patient records found in the database.</p>
                    <p>Add patients using the form.</p>
                </div>';
            } else {
                echo '<table>
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Species</th>
                            <th>Admission Date</th>
                            <th>Condition</th>
                            <th>Status</th>
                            <th>Common Name</th>
                            <th>Weight (g)</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    // Status class
                    $statusClass = 'status-' . strtolower(str_replace(' ', '-', $row['CurrentStatus']));
                    
                    echo '<tr>
                        <td><strong>' . htmlspecialchars($row['PatientID']) . '</strong></td>
                        <td>' . htmlspecialchars($row['SpeciesName'] ?? $row['SpeciesID']) . '</td>
                        <td>' . htmlspecialchars($row['AdmissionDate']) . '</td>
                        <td><span class="condition-badge condition-' . $row['ConditionOnArrival'] . '">' . $row['ConditionOnArrival'] . '</span></td>
                        <td class="' . $statusClass . '">' . htmlspecialchars($row['CurrentStatus']) . '</td>
                        <td>' . (htmlspecialchars($row['CommonName']) ?: '-') . '</td>
                        <td>' . ($row['Weight'] ? number_format($row['Weight'], 2) : '-') . '</td>
                    </tr>';
                }
                
                echo '</tbody></table>';
            }
            ?>
            
            <br>
            <a href="index.php" class="btn">← Back to Add Patient</a>
            
            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h3>📁 Database File Info</h3>
                <p><strong>Database File:</strong> rspca.db</p>
                <p><strong>File Size:</strong> 
                    <?php 
                    if (file_exists('rspca.db')) {
                        echo round(filesize('rspca.db') / 1024, 2) . ' KB';
                    } else {
                        echo 'Not created yet';
                    }
                    ?>
                </p>
                <p><strong>Last Modified:</strong> 
                    <?php 
                    if (file_exists('rspca.db')) {
                        echo date('Y-m-d H:i:s', filemtime('rspca.db'));
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
