<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

try {
    $staff_id = (int)$_GET['staff_id'];
    
    // Calculate total boxes supplied
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(small_boxes), 0) as small_boxes,
            COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM supplies 
        WHERE staff_id = ?
    ");
    $stmt->execute([$staff_id]);
    $supplies = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate total amount due
    $total_amount = ($supplies['small_boxes'] * 300) + ($supplies['big_boxes'] * 500);

    // Get total payments made
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) as paid_amount 
        FROM payments 
        WHERE staff_id = ?
    ");
    $stmt->execute([$staff_id]);
    $payments = $stmt->fetch(PDO::FETCH_ASSOC);

    $pending_amount = $total_amount - $payments['paid_amount'];
    $total_boxes = $supplies['small_boxes'] + $supplies['big_boxes'];

    $response = [
        'pending_amount' => $pending_amount,
        'boxes_count' => $total_boxes,
        'total_supplied' => $total_amount,
        'total_paid' => $payments['paid_amount']
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 