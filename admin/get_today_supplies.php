<?php
require_once '../config/database.php';
header('Content-Type: application/json');

try {
    $today = date('Y-m-d');
    
    $stmt = $pdo->prepare("
        SELECT s.*, st.name as staff_name 
        FROM supplies s
        JOIN staff st ON s.staff_id = st.id
        WHERE s.date = ?
        ORDER BY s.created_at DESC
    ");
    $stmt->execute([$today]);
    $supplies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true, 
        'data' => $supplies
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}