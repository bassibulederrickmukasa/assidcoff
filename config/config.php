<?php
require 'vendor/autoload.php'; // Ensure Composer's autoload is included

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

$connectionParams = [
    'dbname' => 'assidcoff_inventory',
    'user' => 'assidcoff_inventory_user',
    'password' => 'brD1go60CJl8uFK0SOlCkEUZdYRSuG8d',
    'host' => 'dpg-ctal8opu0jms73f0qk00-a.oregon-postgres.render.com',
    'driver' => 'pdo_pgsql',
];

try {
    $conn = DriverManager::getConnection($connectionParams);
    echo "Connected successfully to the database.";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}

if (in_array('pgsql', PDO::getAvailableDrivers())) {
    echo "PostgreSQL PDO driver is available.";
} else {
    echo "PostgreSQL PDO driver is NOT available.";
}

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'dpg-ctal8opu0jms73f0qk00-a.oregon-postgres.render.com');
define('DB_NAME', getenv('DB_NAME') ?: 'assidcoff_inventory');
define('DB_USER', getenv('DB_USER') ?: 'assidcoff_inventory_user');
define('DB_PASS', getenv('DB_PASSWORD') ?: 'brD1go60CJl8uFK0SOlCkEUZdYRSuG8d');
define('DB_PORT', getenv('DB_PORT') ?: '5432');
define('DB_SSL_MODE', getenv('DB_SSL_MODE') ?: 'require');

// PDO connection string
function getDBConnection() {
    try {
        $dsn = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_SSL_MODE
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        // Create a new PDO instance
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (Exception $e) {
        echo "Database connection error: " . $e->getMessage();
    }
}