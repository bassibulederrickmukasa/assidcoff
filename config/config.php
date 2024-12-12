<?php
require_once 'config.php';

try {
    // Create a new PDO instance
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Set character set
    $pdo->exec("SET NAMES 'utf8'");

} catch (PDOException $e) {
    // Handle connection error
    echo "Connection failed: " . $e->getMessage();
    exit; // Stop further execution if the connection fails
}<?php
require_once 'config/database.php';
if ($pdo) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed.";
}