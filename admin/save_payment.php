<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

// Check if user is admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['error' => 'Only administrators can record payments']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

try {
    $date = $_POST['date'];
    $staff_id = (int)$_POST['staff_id'];
    $amount = (float)$_POST['amount'];
    $boxes_count = (int)$_POST['boxes_count'];

    // Calculate current balance
    $stmt = $pdo->prepare("
        SELECT COALESCE(MAX(balance), 0) as current_balance 
        FROM payments 
        WHERE staff_id = ?
    ");
    $stmt->execute([$staff_id]);
    $result = $stmt->fetch();
    $current_balance = $result['current_balance'];

    // Calculate new balance
    $new_balance = $current_balance + $amount;

    // Record the payment
    $stmt = $pdo->prepare("
        INSERT INTO payments (date, staff_id, amount, boxes_count, balance) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$date, $staff_id, $amount, $boxes_count, $new_balance]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 