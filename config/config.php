<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'assidcoff_inventory');
define('DB_USER', 'root');
define('DB_PASS', '');

// System settings
define('SYSTEM_NAME', 'Assidcoff Inventory');
define('COMPANY_NAME', 'Assidcoff');
define('CURRENCY', 'UGX');
define('DATE_FORMAT', 'Y-m-d');
define('TIMEZONE', 'Africa/Kampala');

// Security settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes
define('PASSWORD_MIN_LENGTH', 8);

// Backup settings
define('BACKUP_PATH', 'backups/');
define('MAX_BACKUP_FILES', 10);

// Error reporting
define('ERROR_LOG_PATH', 'logs/error.log');
define('ACTIVITY_LOG_PATH', 'logs/activity.log');

// Initialize settings
date_default_timezone_set(TIMEZONE);

// Session configuration - only set if session hasn't started
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_set_cookie_params(SESSION_LIFETIME);
}

// Create required directories
$directories = [BACKUP_PATH, dirname(ERROR_LOG_PATH), dirname(ACTIVITY_LOG_PATH)];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}