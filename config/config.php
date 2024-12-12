<?php
require 'vendor/autoload.php';

// Use environment variables or secure configuration
$dbConfig = [
    'host' => getenv('DB_HOST') ?: 'your_host',
    'dbname' => getenv('DB_NAME') ?: 'your_database',
    'user' => getenv('DB_USER') ?: 'your_username',
    'password' => getenv('DB_PASS') ?: 'your_password',
    'driver' => 'pdo_pgsql', // or 'pdo_mysql' depending on your database
];

// Centralized connection function
function getDatabaseConnection($config) {
    try {
        // Use output buffering to prevent header issues
        ob_start();

        $dsn = sprintf(
            "%s:host=%s;dbname=%s",
            str_replace('pdo_', '', $config['driver']), // Remove 'pdo_' prefix
            $config['host'], 
            $config['dbname']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, $config['user'], $config['password'], $options);
        
        ob_end_clean(); // Clear output buffer
        return $pdo;

    } catch (PDOException $e) {
        // Log error securely
        error_log("Database Connection Error: " . $e->getMessage());
        die("Database connection failed. Please contact support.");
    }
}

// Verify database driver
function checkDatabaseDriver($driver) {
    $availableDrivers = PDO::getAvailableDrivers();
    if (!in_array($driver, $availableDrivers)) {
        error_log("Database driver $driver is not available. Available drivers: " . implode(', ', $availableDrivers));
        return false;
    }
    return true;
}

// Usage
try {
    // Check driver first
    if (checkDatabaseDriver(str_replace('pdo_', '', $dbConfig['driver']))) {
        $connection = getDatabaseConnection($dbConfig);
    }
} catch (Exception $e) {
    error_log("Initialization Error: " . $e->getMessage());
}