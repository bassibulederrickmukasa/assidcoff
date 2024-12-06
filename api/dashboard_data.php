<?php
// Prevent any output before headers
ob_start();

// Include configuration first to get session settings
require_once '../config/config.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database
require_once '../config/database.php';

// Clear any output buffers and ensure no warnings are output
ob_end_clean();

// Set JSON content type
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $today = date('Y-m-d');
    $response = [];

    // Today's data
    $response['today'] = [
        'production' => getTodayProduction($pdo),
        'stock' => getCurrentStock($pdo),
        'supplies' => getTodaySupplies($pdo),
        'payments' => getTodayPayments($pdo)
    ];

    // Trend data (last 7 days)
    $response['trends'] = getTrends($pdo);

    // System overview for admins
    if ($_SESSION['role'] === 'admin') {
        $response['system'] = getSystemOverview($pdo);
    }

    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database error in dashboard_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} catch (Exception $e) {
    error_log("General error in dashboard_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'System error']);
}

function getTodayProduction($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(small_boxes), 0) as small_boxes,
            COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM daily_production 
        WHERE DATE(created_at) = CURRENT_DATE()
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCurrentStock($pdo) {
    $stmt = $pdo->prepare("
        WITH current_stock AS (
            SELECT 
                COALESCE(SUM(small_boxes), 0) as small_stock,
                COALESCE(SUM(big_boxes), 0) as big_stock
            FROM supplies
            WHERE DATE(created_at) >= CURRENT_DATE() - INTERVAL 30 DAY
        ), current_production AS (
            SELECT 
                COALESCE(SUM(small_boxes), 0) as small_production,
                COALESCE(SUM(big_boxes), 0) as big_production
            FROM daily_production
            WHERE DATE(created_at) >= CURRENT_DATE() - INTERVAL 30 DAY
        )
        SELECT 
            current_production.small_production - current_stock.small_stock as small_boxes,
            current_production.big_production - current_stock.big_stock as big_boxes
        FROM current_stock, current_production
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTodaySupplies($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(small_boxes), 0) as small_boxes,
            COALESCE(SUM(big_boxes), 0) as big_boxes
        FROM supplies
        WHERE DATE(created_at) = CURRENT_DATE()
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTodayPayments($pdo) {
    // Get box prices
    $stmt = $pdo->query("SELECT box_type, price FROM boxes");
    $box_prices = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $small_price = $box_prices['small'] ?? 300;
    $big_price = $box_prices['big'] ?? 500;

    // Get today's total payments
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) as amount
        FROM payments
        WHERE DATE(created_at) = CURRENT_DATE()
    ");
    $stmt->execute();
    $today_payments = $stmt->fetch(PDO::FETCH_ASSOC)['amount'];

    // Calculate total supplied boxes value (last 30 days)
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(small_boxes * :small_price), 0) + 
            COALESCE(SUM(big_boxes * :big_price), 0) as total_value
        FROM supplies
        WHERE DATE(created_at) >= CURRENT_DATE() - INTERVAL 30 DAY
    ");
    $stmt->execute([
        'small_price' => $small_price,
        'big_price' => $big_price
    ]);
    $total_supplied_value = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'];

    // Calculate total payments made
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total_payments FROM payments");
    $total_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total_payments'];

    // Calculate outstanding balance
    $outstanding_balance = $total_supplied_value - $total_payments;

    return [
        'amount' => $today_payments,
        'balance' => $outstanding_balance
    ];
}

function getTrends($pdo) {
    // Get data for last 7 days
    $stmt = $pdo->prepare("
        WITH RECURSIVE dates AS (
            SELECT CURRENT_DATE() - INTERVAL 6 DAY as date
            UNION ALL
            SELECT date + INTERVAL 1 DAY
            FROM dates
            WHERE date < CURRENT_DATE()
        )
        SELECT 
            dates.date,
            COALESCE(p.small_boxes, 0) as small_boxes,
            COALESCE(p.big_boxes, 0) as big_boxes,
            COALESCE(pay.amount, 0) as payment
        FROM dates
        LEFT JOIN (
            SELECT 
                DATE(created_at) as date,
                SUM(small_boxes) as small_boxes,
                SUM(big_boxes) as big_boxes
            FROM daily_production
            GROUP BY DATE(created_at)
        ) p ON dates.date = p.date
        LEFT JOIN (
            SELECT 
                DATE(created_at) as date,
                SUM(amount) as amount
            FROM payments
            GROUP BY DATE(created_at)
        ) pay ON dates.date = pay.date
        ORDER BY dates.date
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for charts
    $dates = [];
    $small_boxes = [];
    $big_boxes = [];
    $payments = [];

    foreach ($results as $row) {
        $dates[] = date('M d', strtotime($row['date']));
        $small_boxes[] = (int)$row['small_boxes'];
        $big_boxes[] = (int)$row['big_boxes'];
        $payments[] = (float)$row['payment'];
    }

    return [
        'dates' => $dates,
        'small_boxes' => $small_boxes,
        'big_boxes' => $big_boxes,
        'payments' => $payments
    ];
}

function getSystemOverview($pdo) {
    // Get total users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $total_users = $stmt->fetch()['count'];

    // Get total revenue (all time)
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments");
    $total_revenue = $stmt->fetch()['total'];

    // Get active staff (supplied in last 30 days)
    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT staff_id) as count 
        FROM supplies 
        WHERE created_at >= CURRENT_DATE() - INTERVAL 30 DAY
    ");
    $active_staff = $stmt->fetch()['count'];

    // Get last backup time
    $stmt = $pdo->query("
        SELECT created_at 
        FROM backups 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $last_backup = $stmt->fetch();
    $last_backup_time = $last_backup ? date('Y-m-d H:i:s', strtotime($last_backup['created_at'])) : 'Never';

    return [
        'total_users' => $total_users,
        'total_revenue' => $total_revenue,
        'active_staff' => $active_staff,
        'last_backup' => $last_backup_time
    ];
}