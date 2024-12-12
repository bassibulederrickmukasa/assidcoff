<?php
// Strict error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

try {
    // PostgreSQL Connection (since your config uses PostgreSQL)
    $host = 'dpg-ctal8opu0jms73f0qk00-a.oregon-postgres.render.com';
    $dbname = 'assidcoff_inventory';
    $username = 'assidcoff_inventory_user';
    $password = 'brD1go60CJl8uFK0SOlCkEUZdYRSuG8d';
    $port = 5432;

    // PDO Connection String
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    // Attempt Connection
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "Database connection successful!";

} catch (PDOException $e) {
    // Detailed error logging
    error_log("Connection Error: " . $e->getMessage());
    die("Database connection failed: " . $e->getMessage());
} finally {
    // Clear output buffer
    ob_end_clean();
}
?> 