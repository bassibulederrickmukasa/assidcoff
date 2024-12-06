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
    $small_price = (float)$_POST['small_price'];
    $big_price = (float)$_POST['big_price'];

    // Update small box price
    $stmt = $pdo->prepare("
        INSERT INTO boxes (box_type, price) VALUES ('small', ?)
        ON DUPLICATE KEY UPDATE price = ?
    ");
    $stmt->execute([$small_price, $small_price]);

    // Update big box price
    $stmt = $pdo->prepare("
        INSERT INTO boxes (box_type, price) VALUES ('big', ?)
        ON DUPLICATE KEY UPDATE price = ?
    ");
    $stmt->execute([$big_price, $big_price]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 