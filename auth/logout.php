<?php
require_once '../config/config.php';

// Move session configuration here
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

session_start();
require_once '../config/database.php';
require_once '../includes/security.php';

if (isset($_SESSION['user_id'])) {
    logActivity($pdo, $_SESSION['user_id'], 'logout', 'User logged out');
}

session_destroy();
header("Location: ../index.php");
exit();
?> 