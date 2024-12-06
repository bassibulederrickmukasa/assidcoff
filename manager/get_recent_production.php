<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/security.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Ensure the table and column names match your database schema
    $stmt = $pdo->prepare("
        SELECT 
            id,
            date,
            small_boxes,
            big_boxes,
            (small_boxes * ? + big_boxes * ?) as total_value
        FROM daily_production 
        ORDER BY date DESC 
        LIMIT 10
    ");
    
    // Replace these values with your actual box values from config or database
    $small_box_value = 10;  // Example value
    $big_box_value = 20;    // Example value
    
    $stmt->execute([$small_box_value, $big_box_value]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($records)) {
        echo json_encode(['success' => true, 'data' => [], 'message' => 'No records found']);
    } else {
        echo json_encode(['success' => true, 'data' => $records]);
    }
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());  // Log to server error log
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error', 
        'details' => $e->getMessage()  // Only for debugging
    ]);
}