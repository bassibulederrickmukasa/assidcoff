<?php
session_start();
require_once '../config/database.php';

// Verify manager access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

try {
    // Fetch recent comments with supply and user details
    $stmt = $pdo->prepare("
        SELECT 
            sc.id,
            sc.comment,
            sc.created_at as date,
            s.id as supply_id,
            st.name as staff_name,
            u.username as commented_by
        FROM supply_comments sc
        JOIN supplies s ON sc.supply_id = s.id
        JOIN staff st ON s.staff_id = st.id
        JOIN users u ON sc.user_id = u.id
        ORDER BY sc.created_at DESC
        LIMIT 10
    ");
    
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates
    foreach ($comments as &$comment) {
        $comment['date'] = date('Y-m-d H:i', strtotime($comment['date']));
    }
    
    header('Content-Type: application/json');
    echo json_encode($comments);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}