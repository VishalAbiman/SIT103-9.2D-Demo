<?php
// config.php - Database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$password = "";  // Default MySQL on Mac has no password
$database = "rspca_wildlife_hospital";

// Try different connection methods
$conn = @new mysqli($host, $user, $password, $database);

// If connection fails, try to create database
if ($conn->connect_error) {
    echo "<p style='color:orange'>⚠️ Database '$database' not found. Attempting to create it...</p>";
    
    // Connect without database first
    $temp_conn = new mysqli($host, $user, $password);
    
    if (!$temp_conn->connect_error) {
        // Create database
        $temp_conn->query("CREATE DATABASE IF NOT EXISTS $database");
        echo "<p style='color:green'>✅ Database created successfully</p>";
        $temp_conn->close();
        
        // Try connecting again
        $conn = new mysqli($host, $user, $password, $database);
        
        if ($conn->connect_error) {
            die("<p style='color:red'>❌ Final connection failed: " . $conn->connect_error . "</p>");
        }
    } else {
        die("<p style='color:red'>❌ Could not connect to MySQL. Please check if MySQL is running.<br>
            Try: <code>brew services start mysql</code> in Terminal</p>");
    }
}

// Optional: Set charset to UTF-8
$conn->set_charset("utf8mb4");

// For debugging (remove in production)
echo "<!-- ✅ Database connection successful -->";

// If you want to hide the success message, remove the echo above
// Or use: // echo "<!-- ✅ Database connection successful -->";
?>