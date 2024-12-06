<?php
// Prevent any output before headers
ob_start();

// Include configuration and start session
require_once '../config/config.php';

// Set session parameters before starting session
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

session_start();
require_once '../config/database.php';

// Clear any output buffers
ob_clean();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

try {
    $today = date('Y-m-d');
    $response = [];

    // Debug connection
    if (!$pdo) {
        error_log("PDO connection is not available");
        throw new Exception("Database connection error");
    }

    // Today's data
    $todayProduction = getTodayProduction($pdo);
    $currentStock = getCurrentStock($pdo);
    $todaySupplies = getTodaySupplies($pdo);
    $todayPayments = getTodayPayments($pdo);

    error_log("Debug data - Production: " . json_encode($todayProduction));
    error_log("Debug data - Stock: " . json_encode($currentStock));
    error_log("Debug data - Supplies: " . json_encode($todaySupplies));
    error_log("Debug data - Payments: " . json_encode($todayPayments));

    $response['today'] = [
        'production' => $todayProduction,
        'stock' => $currentStock,
        'supplies' => $todaySupplies,
        'payments' => $todayPayments
    ];

    // Get trend data and system overview
    $response['trends'] = getTrends($pdo);
    $response['system'] = getSystemOverview($pdo);

    // Set proper JSON header
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

function getTodayProduction($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(small_boxes), 0) as small_boxes, 
                   COALESCE(SUM(big_boxes), 0) as big_boxes 
            FROM daily_production 
            WHERE DATE(date) = CURRENT_DATE()
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: ['small_boxes' => 0, 'big_boxes' => 0];
    } catch (PDOException $e) {
        error_log("Error in getTodayProduction: " . $e->getMessage());
        return ['small_boxes' => 0, 'big_boxes' => 0];
    }
}

function getCurrentStock($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                (COALESCE(p.total_small, 0) - COALESCE(s.total_small, 0)) as small_boxes,
                (COALESCE(p.total_big, 0) - COALESCE(s.total_big, 0)) as big_boxes
            FROM 
                (SELECT 
                    SUM(small_boxes) as total_small,
                    SUM(big_boxes) as total_big
                FROM daily_production) p,
                (SELECT 
                    SUM(small_boxes) as total_small,
                    SUM(big_boxes) as total_big
                FROM supplies) s
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: ['small_boxes' => 0, 'big_boxes' => 0];
    } catch (PDOException $e) {
        error_log("Error in getCurrentStock: " . $e->getMessage());
        return ['small_boxes' => 0, 'big_boxes' => 0];
    }
}

function getTodaySupplies($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(small_boxes), 0) as small_boxes,
                   COALESCE(SUM(big_boxes), 0) as big_boxes
            FROM supplies
            WHERE DATE(date) = CURRENT_DATE()
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: ['small_boxes' => 0, 'big_boxes' => 0];
    } catch (PDOException $e) {
        error_log("Error in getTodaySupplies: " . $e->getMessage());
        return ['small_boxes' => 0, 'big_boxes' => 0];
    }
}

function getTodayPayments($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as amount,
                   (SELECT COALESCE(SUM(amount), 0) FROM payments) as balance
            FROM payments
            WHERE DATE(date) = CURRENT_DATE()
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: ['amount' => 0, 'balance' => 0];
    } catch (PDOException $e) {
        error_log("Error in getTodayPayments: " . $e->getMessage());
        return ['amount' => 0, 'balance' => 0];
    }
}

function getTrends($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                DATE_FORMAT(date, '%Y-%m-%d') as date,
                SUM(small_boxes) as small_boxes,
                SUM(big_boxes) as big_boxes,
                (SELECT COALESCE(SUM(amount), 0) 
                 FROM payments 
                 WHERE DATE(date) = DATE(p.date)) as payments
            FROM daily_production p
            WHERE date >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
            GROUP BY DATE(date)
            ORDER BY date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $trends = [
            'dates' => [],
            'small_boxes' => [],
            'big_boxes' => [],
            'payments' => []
        ];
        
        foreach ($results as $row) {
            $trends['dates'][] = $row['date'];
            $trends['small_boxes'][] = (int)$row['small_boxes'];
            $trends['big_boxes'][] = (int)$row['big_boxes'];
            $trends['payments'][] = (float)$row['payments'];
        }
        
        return $trends;
    } catch (PDOException $e) {
        error_log("Error in getTrends: " . $e->getMessage());
        return [
            'dates' => [],
            'small_boxes' => [],
            'big_boxes' => [],
            'payments' => []
        ];
    }
}

function getSystemOverview($pdo) {
    try {
        // Get total users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $total_users = $stmt->fetch()['count'];

        // Get total revenue
        $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments");
        $total_revenue = $stmt->fetch()['total'];

        // Get active staff
        $stmt = $pdo->query("
            SELECT COUNT(DISTINCT staff_id) as count 
            FROM supplies 
            WHERE date >= CURDATE() - INTERVAL 30 DAY
        ");
        $active_staff = $stmt->fetch()['count'];

        return [
            'total_users' => $total_users,
            'total_revenue' => $total_revenue,
            'active_staff' => $active_staff
        ];
    } catch (PDOException $e) {
        error_log("Error in getSystemOverview: " . $e->getMessage());
        return [
            'total_users' => 0,
            'total_revenue' => 0,
            'active_staff' => 0
        ];
    }
}