<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    exit('Unauthorized');
}

try {
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Record the backup
    $stmt = $pdo->prepare("INSERT INTO backups (filename) VALUES (?)");
    $stmt->execute([$filename]);

    // Set headers for file download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $filename);

    // Get database structure
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $output = '';

    foreach ($tables as $table) {
        // Get create table statement
        $stmt = $pdo->query("SHOW CREATE TABLE $table");
        $row = $stmt->fetch();
        $output .= "\n\n" . $row[1] . ";\n\n";

        // Get table data
        $rows = $pdo->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $fields = implode("','", array_map('addslashes', $row));
            $output .= "INSERT INTO $table VALUES ('$fields');\n";
        }
    }

    echo $output;
    exit();

} catch (PDOException $e) {
    http_response_code(500);
    echo 'Error creating backup: ' . $e->getMessage();
} 