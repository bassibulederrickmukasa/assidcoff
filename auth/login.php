<?php
require_once '../config/config.php';

// Move session configuration here
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

session_start();
require_once '../config/database.php';
require_once '../includes/security.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $ip_address = $_SERVER['REMOTE_ADDR'];

    try {
        // Check for too many login attempts
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_attempts 
            WHERE (username = ? OR ip_address = ?) 
            AND attempted_at > NOW() - INTERVAL ? SECOND
        ");
        $stmt->execute([$username, $ip_address, LOCKOUT_TIME]);
        $result = $stmt->fetch();

        if ($result['attempts'] >= MAX_LOGIN_ATTEMPTS) {
            logActivity($pdo, null, 'login_blocked', "Too many attempts for user: $username");
            http_response_code(429);
            exit('Too many login attempts. Please try again later.');
        }

        // Record login attempt
        $stmt = $pdo->prepare("INSERT INTO login_attempts (username, ip_address) VALUES (?, ?)");
        $stmt->execute([$username, $ip_address]);

        // Verify credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Clear login attempts on successful login
            $stmt = $pdo->prepare("
                DELETE FROM login_attempts 
                WHERE username = ? OR ip_address = ?
            ");
            $stmt->execute([$username, $ip_address]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            logActivity($pdo, $user['id'], 'login', 'Successful login');
            
            header("Location: ../dashboard.php");
            exit();
        } else {
            logActivity($pdo, null, 'login_failed', "Failed login attempt for user: $username");
            header("Location: ../index.php?error=invalid");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login Error: " . $e->getMessage());
        header("Location: ../index.php?error=system");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>