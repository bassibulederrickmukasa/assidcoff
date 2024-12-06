<?php
function checkUserRole($required_role) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    if ($_SESSION['role'] !== $required_role && $_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php");
        exit();
    }
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function sanitizeNumber($number) {
    return filter_var($number, FILTER_VALIDATE_FLOAT, 
        ["options" => ["min_range" => 0]]) !== false ? $number : 0;
}

function validateBoxCount($count) {
    return filter_var($count, FILTER_VALIDATE_INT, 
        ["options" => ["min_range" => 0]]) !== false;
}

function logActivity($pdo, $user_id, $action, $details) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, details) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user_id, $action, $details]);
    } catch (PDOException $e) {
        // Silent fail - don't interrupt user operation for logging
        error_log("Error logging activity: " . $e->getMessage());
    }
} 