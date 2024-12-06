<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Session security checks
    if (isset($_SESSION['user_id'])) {
        if (!isset($_SESSION['last_activity'])) {
            session_destroy();
            header("Location: index.php?error=timeout");
            exit();
        }

        if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
            session_destroy();
            header("Location: index.php?error=timeout");
            exit();
        }

        $_SESSION['last_activity'] = time();
    }

} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}
?> 