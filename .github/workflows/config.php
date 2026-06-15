<?php
/**
 * Database Configuration File
 * This file handles all database connections for the PetCare Store
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'petcare_store');

// Attempt to create connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage() . 
        "<br><a href='db_setup.php'>Click here to initialize the database</a>");
}

// Check if database exists, if not create it
function initializeDatabase() {
    $temp_conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($temp_conn->connect_error) {
        return false;
    }
    
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if (!$temp_conn->query($sql)) {
        return false;
    }
    
    $temp_conn->close();
    return true;
}

?>
