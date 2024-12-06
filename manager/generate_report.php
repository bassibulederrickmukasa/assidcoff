<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$type = $_GET['type'];
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$export = isset($_GET['export']) && $_GET['export'] === 'true';

try {
    $data = [];
    
    switch($type) {
        case 'production':
            $stmt = $pdo->prepare("
                SELECT date, small_boxes, big_boxes,
                (small_boxes * 300 + big_boxes * 500) as total_value
                FROM daily_production
                WHERE date BETWEEN ? AND ?
                ORDER BY date DESC
            ");
            break;

        case 'supplies':
            $stmt = $pdo->prepare("
                SELECT s.date, st.name as staff_name, 
                s.small_boxes, s.big_boxes,
                (s.small_boxes * 300 + s.big_boxes * 500) as value
                FROM supplies s
                JOIN staff st ON s.staff_id = st.id
                WHERE s.date BETWEEN ? AND ?
                ORDER BY s.date DESC
            ");
            break;

        case 'payments':
            $stmt = $pdo->prepare("
                SELECT p.date, st.name as staff_name,
                p.amount, p.boxes_count, p.balance
                FROM payments p
                JOIN staff st ON p.staff_id = st.id
                WHERE p.date BETWEEN ? AND ?
                ORDER BY p.date DESC
            ");
            break;

        case 'staff':
            $stmt = $pdo->prepare("
                SELECT st.name,
                SUM(s.small_boxes) as total_small,
                SUM(s.big_boxes) as total_big,
                SUM(s.small_boxes * 300 + s.big_boxes * 500) as total_value,
                SUM(p.amount) as total_paid
                FROM staff st
                LEFT JOIN supplies s ON st.id = s.staff_id
                LEFT JOIN payments p ON st.id = p.staff_id
                WHERE (s.date BETWEEN ? AND ?) OR (p.date BETWEEN ? AND ?)
                GROUP BY st.id, st.name
            ");
            $stmt->execute([$startDate, $endDate, $startDate, $endDate]);
            break;
    }

    if ($type !== 'staff') {
        $stmt->execute([$startDate, $endDate]);
    }
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($export) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report.csv"');
        $output = fopen('php://output', 'w');
        
        // Add headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
        }
        
        // Add data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    }

    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 