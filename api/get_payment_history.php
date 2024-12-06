<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    // Get payment history with user details
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.payment_date,
            p.amount,
            m.username as manager_name,
            a.username as recorded_by
        FROM payments p
        JOIN users m ON p.manager_id = m.id
        JOIN users a ON p.recorded_by = a.id
        ORDER BY p.payment_date DESC, p.created_at DESC
        LIMIT 20
    ");
    
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $payments
    ]);

} catch (Exception $e) {
    error_log("Error in get_payment_history.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch payment history: ' . $e->getMessage()
    ]);
}