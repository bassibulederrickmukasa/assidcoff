<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

try {
    $today = date('Y-m-d');
    $response = [];
    
    // Get box prices
    $stmt = $pdo->query("SELECT box_type, price FROM boxes");
    $boxes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $small_price = $boxes['small'] ?? 300;
    $big_price = $boxes['big'] ?? 500;

    // Get today's production
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(small_boxes), 0) as small_boxes,
               COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM daily_production 
        WHERE date = ?
    ");
    $stmt->execute([$today]);
    $response['today'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get this week's production (last 7 days)
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(small_boxes), 0) as small_boxes,
               COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM daily_production 
        WHERE date >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
    ");
    $stmt->execute();
    $response['week'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get this month's production
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(small_boxes), 0) as small_boxes,
               COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM daily_production 
        WHERE YEAR(date) = YEAR(CURRENT_DATE()) 
        AND MONTH(date) = MONTH(CURRENT_DATE())
    ");
    $stmt->execute();
    $response['month'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate total values
    foreach (['today', 'week', 'month'] as $period) {
        if (!isset($response[$period])) {
            $response[$period] = ['small_boxes' => 0, 'big_boxes' => 0];
        }
        $response[$period]['total_value'] = 
            ($response[$period]['small_boxes'] * $small_price) + 
            ($response[$period]['big_boxes'] * $big_price);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database error in get_production_summary.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}