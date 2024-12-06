<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

try {
    // Get last 7 days of production data
    $stmt = $pdo->prepare("
        WITH RECURSIVE dates AS (
            SELECT CURRENT_DATE() as date
            UNION ALL
            SELECT DATE_SUB(date, INTERVAL 1 DAY)
            FROM dates
            WHERE DATE_SUB(date, INTERVAL 1 DAY) >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY)
        )
        SELECT 
            dates.date,
            COALESCE(p.small_boxes, 0) as small_boxes,
            COALESCE(p.big_boxes, 0) as big_boxes
        FROM dates
        LEFT JOIN (
            SELECT 
                date,
                SUM(small_boxes) as small_boxes,
                SUM(big_boxes) as big_boxes
            FROM daily_production
            GROUP BY date
        ) p ON dates.date = p.date
        ORDER BY dates.date ASC
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'dates' => [],
        'small_boxes' => [],
        'big_boxes' => []
    ];

    foreach ($results as $row) {
        $response['dates'][] = date('M j', strtotime($row['date']));
        $response['small_boxes'][] = (int)$row['small_boxes'];
        $response['big_boxes'][] = (int)$row['big_boxes'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database error in get_production_trend.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}