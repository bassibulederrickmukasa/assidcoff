<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

try {
    // Get recent payments with staff names
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            s.name as staff_name
        FROM payments p
        JOIN staff s ON p.staff_id = s.id
        ORDER BY p.date DESC, p.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($payments);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 