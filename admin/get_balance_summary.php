<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

try {
    // Get all staff with their balances
    $stmt = $pdo->prepare("
        SELECT 
            s.id,
            s.name,
            COALESCE(MAX(p.balance), 0) as balance,
            MAX(p.date) as last_payment_date,
            (
                SELECT (COALESCE(SUM(sup.small_boxes), 0) * 300 + 
                       COALESCE(SUM(sup.big_boxes), 0) * 500) -
                       COALESCE(SUM(p2.amount), 0)
                FROM supplies sup
                LEFT JOIN payments p2 ON sup.staff_id = p2.staff_id
                WHERE sup.staff_id = s.id
            ) as pending_amount
        FROM staff s
        LEFT JOIN payments p ON s.id = p.staff_id
        GROUP BY s.id, s.name
        ORDER BY pending_amount DESC
    ");
    $stmt->execute();
    $staff_balances = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($staff_balances as &$staff) {
        $staff['last_payment'] = $staff['last_payment_date'] ? 
            date('Y-m-d', strtotime($staff['last_payment_date'])) : null;
        unset($staff['last_payment_date']);
    }

    header('Content-Type: application/json');
    echo json_encode($staff_balances);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 