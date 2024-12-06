<?php
session_start();
require_once '../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Debug session
    error_log('Session data: ' . print_r($_SESSION, true));
    
    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    // Get filter parameter
    $filter = $_GET['filter'] ?? 'today';

    // Debug received parameters
    error_log('Filter: ' . $filter);

    // Prepare date condition based on filter
    switch ($filter) {
        case 'week':
            $dateCondition = "s.date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
            break;
        case 'month':
            $dateCondition = "s.date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";
            break;
        default: // today
            $dateCondition = "s.date = CURRENT_DATE";
    }

    // Get box prices for value calculation
    $stmt = $pdo->prepare("SELECT box_type, price FROM boxes");
    $stmt->execute();
    $prices = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $prices[$row['box_type']] = $row['price'];
    }

    // Fetch supplies with staff names and comments
    $stmt = $pdo->prepare("
        SELECT 
            s.id,
            s.date,
            s.small_boxes,
            s.big_boxes,
            st.name as staff_name,
            GROUP_CONCAT(
                CONCAT(
                    'Comment by ', u.username, ' on ', 
                    DATE_FORMAT(sc.created_at, '%Y-%m-%d %H:%i'), 
                    ': ', sc.comment
                )
                ORDER BY sc.created_at DESC
                SEPARATOR '\n'
            ) as comments
        FROM supplies s
        LEFT JOIN staff st ON s.staff_id = st.id
        LEFT JOIN supply_comments sc ON s.id = sc.supply_id
        LEFT JOIN users u ON sc.user_id = u.id
        WHERE {$dateCondition}
        GROUP BY s.id, s.date, s.small_boxes, s.big_boxes, st.name
        ORDER BY s.date DESC, s.id DESC
    ");

    $stmt->execute();
    $supplies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total value for each supply
    foreach ($supplies as &$supply) {
        $supply['total_value'] = number_format(
            ($supply['small_boxes'] * $prices['small']) + 
            ($supply['big_boxes'] * $prices['big'])
        );
    }

    // Debug the data we're about to send
    error_log('Supplies data: ' . print_r($supplies, true));

    // Format the response
    $response = [
        'success' => true,
        'data' => [
            'supplies' => $supplies,
            'filter' => $filter
        ]
    ];

    // Debug final response
    error_log('Final response: ' . print_r($response, true));
    
    echo json_encode($response);

} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('General Error: ' . $e->getMessage());
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Authentication error',
        'message' => $e->getMessage()
    ]);
}