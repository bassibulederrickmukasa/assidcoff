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
        WITH current_stock AS (
            SELECT 
                COALESCE(SUM(small_boxes), 0) as small_stock,
                COALESCE(SUM(big_boxes), 0) as big_stock
            FROM supplies
            WHERE DATE(created_at) >= CURRENT_DATE() - INTERVAL 30 DAY
        ), current_production AS (
            SELECT 
                COALESCE(SUM(small_boxes), 0) as small_production,
                COALESCE(SUM(big_boxes), 0) as big_production
            FROM daily_production
            WHERE DATE(created_at) >= CURRENT_DATE() - INTERVAL 30 DAY
        )
        SELECT 
            current_stock.small_stock,
            current_stock.big_stock,
            current_production.small_production,
            current_production.big_production
        FROM current_stock, current_production
    ");
    $stmt->execute();
    $stock_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate available stock
    $available_small_stock = $stock_info['small_production'] - $stock_info['small_stock'];
    $available_big_stock = $stock_info['big_production'] - $stock_info['big_stock'];

    if ($small_boxes > $available_small_stock || $big_boxes > $available_big_stock) {
        http_response_code(400);
        exit(json_encode([
            'error' => 'Insufficient stock', 
            'details' => [
                'available_small_stock' => $available_small_stock,
                'available_big_stock' => $available_big_stock,
                'requested_small_boxes' => $small_boxes,
                'requested_big_boxes' => $big_boxes
            ]
        ]));
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