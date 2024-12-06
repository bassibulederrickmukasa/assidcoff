<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

// Verify user is a manager
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['role'] !== 'manager') {
    http_response_code(403);
    exit(json_encode(['error' => 'Only managers can add comments']));
}

try {
    $supply_id = $_POST['supply_id'];
    $comment = $_POST['comment'];
    
    $stmt = $pdo->prepare("INSERT INTO supply_comments (supply_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$supply_id, $_SESSION['user_id'], $comment]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}