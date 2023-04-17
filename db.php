<?php
    // Database configuration
    $dbHost = 'localhost';
    $dbName = 'erep';
    $dbUsername = 'erep_admin';
    $dbPassword = 'ZFy5ng7Zhah9Dx2t';

    // Create a new database connection
    $conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

    // Check for errors
    if (!$conn) {
        die('Connection failed: ' . mysqli_connect_error());
    }

?>