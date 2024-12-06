<?php
// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

try {
    $date = $_GET['date'] ?? date('Y-m-d');
    
    // First verify if tables exist
    try {
        $pdo->query("SELECT 1 FROM supplies LIMIT 1");
        $pdo->query("SELECT 1 FROM staff LIMIT 1");
    } catch (PDOException $e) {
        throw new Exception("Required tables do not exist. Please run database_schema.sql first.");
    }
    
    // Debug information
    error_log("Debug: Attempting to fetch supplies for date: " . $date);
    
    // Get supplies with staff names
    $stmt = $pdo->prepare("
        SELECT s.*, st.name as staff_name,
        (s.small_boxes * 300 + s.big_boxes * 500) as value
        FROM supplies s
        JOIN staff st ON s.staff_id = st.id
        WHERE s.date = ?
        ORDER BY s.created_at DESC
    ");
    error_log("Debug: Query prepared");
    
    $stmt->execute([$date]);
    error_log("Debug: Query executed");
    
    $supplies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Debug: Data fetched. Number of rows: " . count($supplies));

    // Calculate totals
    $totals = [
        'small_boxes' => 0,
        'big_boxes' => 0,
        'value' => 0
    ];

    foreach ($supplies as $supply) {
        $totals['small_boxes'] += $supply['small_boxes'];
        $totals['big_boxes'] += $supply['big_boxes'];
        $totals['value'] += $supply['value'];
    }

    $response = [
        'supplies' => $supplies,
        'totals' => $totals
    ];

    error_log("Debug: Sending response");
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database Error in get_supplies.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General Error in get_supplies.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 