<?php
// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'dpg-ctal8opu0jms73f0qk00-a.oregon-postgres.render.com');
define('DB_NAME', getenv('DB_NAME') ?: 'assidcoff_inventory');
define('DB_USER', getenv('DB_USER') ?: 'assidcoff_inventory_user');
define('DB_PASS', getenv('DB_PASSWORD') ?: 'brD1go60CJl8uFK0SOlCkEUZdYRSuG8d');
define('DB_PORT', getenv('DB_PORT') ?: '5432');
define('DB_SSL_MODE', getenv('DB_SSL_MODE') ?: 'require');

// PDO connection string
function getDBConnection() {
    try {
        $dsn = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_SSL_MODE
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Log the error
        error_log(sprintf(
            "Database connection failed: %s\nTrace: %s",
            $e->getMessage(),
            $e->getTraceAsString()
        ));
        
        // In production, don't expose error details
        if (getenv('APP_ENV') === 'production') {
            throw new Exception('Database connection failed. Please try again later.');
        }
        
        throw $e;
    }
}

// System settings
define('SYSTEM_NAME', getenv('SYSTEM_NAME') ?: 'Assidcoff Inventory');
define('COMPANY_NAME', getenv('COMPANY_NAME') ?: 'Assidcoff');
define('CURRENCY', getenv('CURRENCY') ?: 'UGX');
define('DATE_FORMAT', getenv('DATE_FORMAT') ?: 'Y-m-d');
define('TIMEZONE', getenv('TIMEZONE') ?: 'Africa/Kampala');

// Security settings
define('SESSION_LIFETIME', getenv('SESSION_LIFETIME') ?: 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', getenv('MAX_LOGIN_ATTEMPTS') ?: 5);
define('LOCKOUT_TIME', getenv('LOCKOUT_TIME') ?: 900); // 15 minutes
define('PASSWORD_MIN_LENGTH', getenv('PASSWORD_MIN_LENGTH') ?: 8);

// Backup settings
define('BACKUP_PATH', getenv('BACKUP_PATH') ?: 'backups/');
define('MAX_BACKUP_FILES', getenv('MAX_BACKUP_FILES') ?: 10);

// Error reporting
define('ERROR_LOG_PATH', getenv('ERROR_LOG_PATH') ?: 'logs/error.log');
define('ACTIVITY_LOG_PATH', getenv('ACTIVITY_LOG_PATH') ?: 'logs/activity.log');

// Initialize settings
date_default_timezone_set(TIMEZONE);

// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_set_cookie_params(SESSION_LIFETIME);
    session_start();
}

// Create required directories
$directories = [BACKUP_PATH, dirname(ERROR_LOG_PATH), dirname(ACTIVITY_LOG_PATH)];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}