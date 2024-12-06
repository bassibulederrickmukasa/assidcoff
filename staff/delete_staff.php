<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

try {
    $id = (int)$_POST['id'];

    // Check if staff has any supplies or payments
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as record_count 
        FROM (
            SELECT staff_id FROM supplies WHERE staff_id = ?
            UNION
            SELECT staff_id FROM payments WHERE staff_id = ?
        ) as records
    ");
    $stmt->execute([$id, $id]);
    $result = $stmt->fetch();

    if ($result['record_count'] > 0) {
        http_response_code(400);
        exit(json_encode(['error' => 'Cannot delete staff member with existing records']));
    }

    $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 