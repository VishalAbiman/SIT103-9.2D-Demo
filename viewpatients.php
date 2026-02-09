<?php
// view_patients.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!file_exists('rspca.db')) {
    die("No database found. Add patients first.");
}

$db = new SQLite3('rspca.db');
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Patients</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #2c3e50; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Saved Patient Records</h1>
    
    <?php
    $result = $db->query("SELECT * FROM PATIENT ORDER BY AdmissionDate DESC");
    
    if (!$result) {
        echo "<p>No patients found.</p>";
    } else {
        echo "<table>
            <tr>
                <th>Patient ID</th>
                <th>Species</th>
                <th>Admission Date</th>
                <th>Condition</th>
                <th>Status</th>
            </tr>";
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            echo "<tr>
                <td>{$row['PatientID']}</td>
                <td>{$row['SpeciesID']}</td>
                <td>{$row['AdmissionDate']}</td>
                <td>{$row['ConditionOnArrival']}</td>
                <td>{$row['CurrentStatus']}</td>
            </tr>";
        }
        echo "</table>";
    }
    ?>
    
    <br>
    <a href="index.php" class="back-btn">← Back to Form</a>
</body>
</html>
