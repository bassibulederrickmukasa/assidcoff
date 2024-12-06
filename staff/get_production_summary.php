<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/security.php';

// Start session after loading config
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Get today's production
    $today = date('Y-m-d');
    $week_start = date('Y-m-d', strtotime('monday this week'));
    $month_start = date('Y-m-01');

    // Today's production
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(small_boxes), 0) as small_boxes,
            COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM daily_production 
        WHERE date = ?
    ");
    $stmt->execute([$today]);
    $today_production = $stmt->fetch(PDO::FETCH_ASSOC);

    // This week's production
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(small_boxes), 0) as small_boxes,
            COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM daily_production 
        WHERE date >= ?
    ");
    $stmt->execute([$week_start]);
    $week_production = $stmt->fetch(PDO::FETCH_ASSOC);

    // This month's production
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(small_boxes), 0) as small_boxes,
            COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM daily_production 
        WHERE date >= ?
    ");
    $stmt->execute([$month_start]);
    $month_production = $stmt->fetch(PDO::FETCH_ASSOC);

    $response = [
        'small_boxes' => [
            'today' => (int)$today_production['small_boxes'],
            'week' => (int)$week_production['small_boxes'],
            'month' => (int)$month_production['small_boxes']
        ],
        'big_boxes' => [
            'today' => (int)$today_production['big_boxes'],
            'week' => (int)$week_production['big_boxes'],
            'month' => (int)$month_production['big_boxes']
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log($e->getMessage());
}