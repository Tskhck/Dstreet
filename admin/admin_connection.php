<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "a.dstreetweardatabase";
    
    try {
        // Create PDO connection
        $con = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Set the PDO error mode to exception
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
?>