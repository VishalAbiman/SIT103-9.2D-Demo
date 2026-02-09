<?php
// viewpatients.php - View all saved patient records
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

// Get database statistics
$totalPatients = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT");
$criticalCount = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT WHERE CurrentStatus = 'Critical'");
$stableCount = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT WHERE CurrentStatus = 'Stable'");
$rehabCount = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT WHERE CurrentStatus = 'Rehabilitating'");
$readyCount = $db->querySingle("SELECT COUNT(*) as count FROM PATIENT WHERE CurrentStatus = 'Ready for Release'");

// Get all patients with species information
$query = "
    SELECT 
        p.PatientID,
        p.SpeciesID,
        s.CommonName as SpeciesName,
        p.AdmissionDate,
        p.ConditionOnArrival,
        p.CurrentStatus,
        p.CommonName,
        p.Weight,
        p.Injuries,
        p.CreatedAt
    FROM PATIENT p
    LEFT JOIN SPECIES s ON p.SpeciesID = s.SpeciesID
    ORDER BY p.AdmissionDate DESC, p.CreatedAt DESC
";

$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records - RSPCA Wildlife Hospital</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #9b59b6;
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
            max-width: 1400px;
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
        
        .content {
            padding: 40px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 25px 20px;
            border-radius: 12px;
            text-align: center;
            border-top: 4px solid var(--secondary-color);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card.critical { border-top-color: var(--danger-color); }
        .stat-card.stable { border-top-color: var(--success-color); }
        .stat-card.rehab { border-top-color: var(--warning-color); }
        .stat-card.ready { border-top-color: var(--info-color); }
        
        .stat-number {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .stat-card.critical .stat-number { color: var(--danger-color); }
        .stat-card.stable .stat-number { color: var(--success-color); }
        .stat-card.rehab .stat-number { color: var(--warning-color); }
        .stat-card.ready .stat-number { color: var(--info-color); }
        
        .stat-label {
            font-size: 1rem;
            color: #666;
            font-weight: 600;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        
        thead {
            background: var(--primary-color);
            color: white;
        }
        
        th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 1rem;
            border-bottom: 2px solid #dee2e6;
        }
        
        td {
            padding: 16px 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        
        tbody tr {
            transition: background 0.2s;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .patient-id {
            font-family: 'Monaco', 'Courier New', monospace;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .condition-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            min-width: 40px;
            text-align: center;
        }
        
        .condition-1 { background: var(--danger-color); color: white; }
        .condition-2 { background: #e67e22; color: white; }
        .condition-3 { background: var(--warning-color); color: #333; }
        .condition-4 { background: #2ecc71; color: white; }
        .condition-5 { background: var(--success-color); color: white; }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-critical { background: #fdeaea; color: var(--danger-color); }
        .status-stable { background: #eaf9f0; color: var(--success-color); }
        .status-rehabilitating { background: #fef5e7; color: var(--warning-color); }
        .status-ready { background: #f4ecf7; color: var(--info-color); }
        
        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #dee2e6;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-export {
            background: #7f8c8d;
            color: white;
        }
        
        .btn-export:hover {
            background: #6c7b7d;
        }
        
        .database-info {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-top: 30px;
            border: 1px solid #dee2e6;
        }
        
        .database-info h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            
            header {
                padding: 20px;
            }
            
            header h1 {
                font-size: 2rem;
                flex-direction: column;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .actions {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>
                <span>📋 Patient Records Database</span>
            </h1>
            <p>RSPCA Wildlife Hospital - All Saved Patient Information</p>
        </header>
        
        <div class="content">
            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalPatients; ?></div>
                    <div class="stat-label">Total Patients</div>
                </div>
                
                <div class="stat-card critical">
                    <div class="stat-number"><?php echo $criticalCount; ?></div>
                    <div class="stat-label">Critical</div>
                </div>
                
                <div class="stat-card stable">
                    <div class="stat-number"><?php echo $stableCount; ?></div>
                    <div class="stat-label">Stable</div>
                </div>
                
                <div class="stat-card rehab">
                    <div class="stat-number"><?php echo $rehabCount; ?></div>
                    <div class="stat-label">Rehabilitating</div>
                </div>
                
                <div class="stat-card ready">
                    <div class="stat-number"><?php echo $readyCount; ?></div>
                    <div class="stat-label">Ready for Release</div>
                </div>
            </div>
            
            <!-- Patient Records Table -->
            <?php if ($totalPatients > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Species</th>
                                <th>Admission Date</th>
                                <th>Condition</th>
                                <th>Status</th>
                                <th>Common Name</th>
                                <th>Weight (g)</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
                                <tr>
                                    <td>
                                        <span class="patient-id"><?php echo htmlspecialchars($row['PatientID']); ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        echo htmlspecialchars($row['SpeciesName'] ?? $row['SpeciesID']);
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['AdmissionDate']); ?></td>
                                    <td>
                                        <span class="condition-badge condition-<?php echo $row['ConditionOnArrival']; ?>">
                                            <?php echo $row['ConditionOnArrival']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = strtolower(str_replace(' ', '-', $row['CurrentStatus']));
                                        echo '<span class="status-badge status-' . $statusClass . '">';
                                        echo htmlspecialchars($row['CurrentStatus']);
                                        echo '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['CommonName'] ?: '-'); ?></td>
                                    <td>
                                        <?php 
                                        if ($row['Weight']) {
                                            echo number_format($row['Weight'], 2);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($row['Injuries']) {
                                            $shortNotes = strlen($row['Injuries']) > 50 ? 
                                                substr($row['Injuries'], 0, 50) . '...' : 
                                                $row['Injuries'];
                                            echo '<span title="' . htmlspecialchars($row['Injuries']) . '">';
                                            echo htmlspecialchars($shortNotes);
                                            echo '</span>';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No Patient Records Found</h3>
                    <p>The database is currently empty. Add your first patient using the form.</p>
                    <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">
                        <span>➕ Add First Patient</span>
                    </a>
                </div>
            <?php endif; ?>
            
            <!-- Actions -->
            <div class="actions">
                <a href="index.php" class="btn btn-primary">
                    <span>← Back to Form</span>
                </a>
                
                <?php if ($totalPatients > 0): ?>
                    <button onclick="exportToCSV()" class="btn btn-export">
                        <span>📥 Export as CSV</span>
                    </button>
                <?php endif; ?>
            </div>
            
            <!-- Database Information -->
            <div class="database-info">
                <h3>💾 Database Information</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div>
                        <p><strong>Database Type:</strong> SQLite 3</p>
                        <p><strong>File Location:</strong> ../rspca.db</p>
                    </div>
                    <div>
                        <p><strong>File Size:</strong> 
                            <?php 
                            if (file_exists('../rspca.db')) {
                                $size = filesize('../rspca.db');
                                if ($size < 1024) {
                                    echo $size . ' bytes';
                                } elseif ($size < 1048576) {
                                    echo round($size / 1024, 2) . ' KB';
                                } else {
                                    echo round($size / 1048576, 2) . ' MB';
                                }
                            } else {
                                echo 'Not created yet';
                            }
                            ?>
                        </p>
                        <p><strong>Last Modified:</strong> 
                            <?php 
                            if (file_exists('../rspca.db')) {
                                echo date('Y-m-d H:i:s', filemtime('../rspca.db'));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Export to CSV function
        function exportToCSV() {
            // This is a simple CSV export - in a real application, 
            // you would implement server-side CSV generation
            alert('CSV export would be implemented in a production system.\n\nFor this demo, the data is saved in the SQLite database file.');
            
            // For a real implementation, you would:
            // 1. Make an AJAX request to a PHP script that generates CSV
            // 2. Or redirect to a PHP script that outputs CSV headers
        }
        
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('click', function() {
                    const patientId = this.querySelector('.patient-id').textContent;
                    alert('Patient ID: ' + patientId + '\n\nIn a full application, this would open detailed view.');
                });
            });
        });
    </script>
</body>
</html>
