<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

try {
    $date = $_POST['date'];
    $staff_id = (int)$_POST['staff_id'];
    $small_boxes = (int)$_POST['small_boxes'];
    $big_boxes = (int)$_POST['big_boxes'];

    // Verify stock availability
    $stmt = $pdo->prepare("
        SELECT 
            (SELECT COALESCE(SUM(small_boxes), 0) FROM daily_production) - 
            (SELECT COALESCE(SUM(small_boxes), 0) FROM supplies) as small_stock,
            (SELECT COALESCE(SUM(big_boxes), 0) FROM daily_production) - 
            (SELECT COALESCE(SUM(big_boxes), 0) FROM supplies) as big_stock
    ");
    $stmt->execute();
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($small_boxes > $stock['small_stock'] || $big_boxes > $stock['big_stock']) {
        http_response_code(400);
        exit(json_encode(['error' => 'Insufficient stock']));
    }

    // Record the supply
    $stmt = $pdo->prepare("
        INSERT INTO supplies (date, staff_id, small_boxes, big_boxes) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$date, $staff_id, $small_boxes, $big_boxes]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 