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
    exit(json_encode(['error' => 'Only administrators can modify production records']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

try {
    $date = $_POST['date'];
    $small_boxes = (int)$_POST['small_boxes'];
    $big_boxes = (int)$_POST['big_boxes'];

    // Check if entry already exists for this date
    $stmt = $pdo->prepare("SELECT id FROM daily_production WHERE date = ?");
    $stmt->execute([$date]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing record
        $stmt = $pdo->prepare("
            UPDATE daily_production 
            SET small_boxes = ?, big_boxes = ? 
            WHERE date = ?
        ");
        $stmt->execute([$small_boxes, $big_boxes, $date]);
    } else {
        // Insert new record
        $stmt = $pdo->prepare("
            INSERT INTO daily_production (date, small_boxes, big_boxes) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$date, $small_boxes, $big_boxes]);
    }

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 