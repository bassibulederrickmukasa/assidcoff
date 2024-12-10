<?php
require_once 'config/config.php';

try {
    // Attempt to connect
    $db = getDBConnection();
    echo "✅ Successfully connected to PostgreSQL database!\n";
    
    // Test query to verify schema
    $tables = $db->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public'
    ")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\nFound tables:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
