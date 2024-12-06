<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    exit('Unauthorized');
}

try {
    // Get total records
    $tables = ['users', 'staff', 'daily_production', 'supplies', 'payments'];
    $total_records = 0;

    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        $total_records += $result['count'];
    }

    // Get database size
    $stmt = $pdo->query("
        SELECT table_schema AS db_name, 
        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size 
        FROM information_schema.tables 
        WHERE table_schema = 'assidcoff_inventory'
        GROUP BY table_schema
    ");
    $size_info = $stmt->fetch();

    // Get last backup info
    $stmt = $pdo->query("
        SELECT created_at 
        FROM backups 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $backup_info = $stmt->fetch();

    $response = [
        'total_records' => $total_records,
        'db_size' => ($size_info['size'] ?? 0) . ' MB',
        'last_backup' => $backup_info ? date('Y-m-d H:i:s', strtotime($backup_info['created_at'])) : null
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 