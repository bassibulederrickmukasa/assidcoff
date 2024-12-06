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
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO staff (name, contact, role) VALUES (?, ?, ?)");
    $stmt->execute([$name, $contact, $role]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 