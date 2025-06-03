<?php

$host = 'localhost';
$username = 'root';
$password = '123456';
$database = 'clinic_system';

try {
    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    echo "Connected successfully\n";
    
    // Try to create the database if it doesn't exist
    $conn->query("CREATE DATABASE IF NOT EXISTS clinic_system");
    
    // Select the database
    $conn->select_db('clinic_system');
    
    echo "Database selected successfully\n";
    
    // Close connection
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 