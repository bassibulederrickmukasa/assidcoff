<?php
session_start();
require_once '../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Basic validation
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    // Get filter parameter
    $filter = $_GET['filter'] ?? 'today';
    $search = $_GET['search'] ?? '';
    
    // Get current date for debugging
    $currentDate = date('Y-m-d');
    
    // Prepare date condition
    switch ($filter) {
        case 'week':
            $dateCondition = "date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
            break;
        case 'month':
            $dateCondition = "date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";
            break;
        case 'today':
            $dateCondition = "date = CURRENT_DATE";
            break;
        default:
            $dateCondition = "1=1"; // Show all records
    }

    // Get all records count (before filtering)
    $totalAllRecords = $pdo->query("SELECT COUNT(*) FROM daily_production")->fetchColumn();

    // Get filtered records count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM daily_production WHERE {$dateCondition}");
    $stmt->execute();
    $total = $stmt->fetchColumn();

    // Get paginated records
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    // Get all dates in the database for debugging
    $allDates = $pdo->query("SELECT DISTINCT date FROM daily_production ORDER BY date DESC")->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare("
        SELECT *
        FROM daily_production 
        WHERE {$dateCondition}
        ORDER BY date DESC, id DESC
        LIMIT :limit OFFSET :offset
    ");
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process records
    $processedRecords = array_map(function($record) {
        // Calculate type
        if ($record['small_boxes'] > 0 && $record['big_boxes'] > 0) {
            $type = 'Mixed';
        } elseif ($record['small_boxes'] > 0) {
            $type = 'Small';
        } else {
            $type = 'Big';
        }

        return [
            'id' => $record['id'],
            'date' => $record['date'],
            'small_boxes' => $record['small_boxes'],
            'big_boxes' => $record['big_boxes'],
            'staff_name' => $_SESSION['username'] ?? 'System',
            'email' => $_SESSION['email'] ?? '-',
            'phone' => $_SESSION['phone'] ?? '-',
            'type' => $type,
            'status' => 'Completed',
            'can_edit' => isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff'])
        ];
    }, $records);

    // Debug information
    $debug = [
        'current_system_date' => $currentDate,
        'filter_applied' => $filter,
        'date_condition' => $dateCondition,
        'total_records_in_db' => $totalAllRecords,
        'filtered_records' => $total,
        'all_dates_in_db' => $allDates,
        'current_page' => $page,
        'limit' => $limit,
        'offset' => $offset,
        'records_found' => count($records),
        'session' => [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'unknown',
            'role' => $_SESSION['role'] ?? 'unknown'
        ]
    ];

    echo json_encode([
        'success' => true,
        'debug' => $debug,
        'data' => $processedRecords,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => ceil($total / $limit)
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage(),
        'debug' => [
            'error_code' => $e->getCode(),
            'error_info' => $pdo->errorInfo()
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'General error',
        'message' => $e->getMessage()
    ]);
}