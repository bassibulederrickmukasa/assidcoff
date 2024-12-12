<?php
// Strict error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent output buffering issues
ob_start();

class DatabaseConnection {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $port;
    private $driver;

    public function __construct($config = []) {
        $this->host = $config['host'] ?? 'localhost';
        $this->dbname = $config['dbname'] ?? '';
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->port = $config['port'] ?? '5432';
        $this->driver = $config['driver'] ?? 'pgsql';
    }

    public function connect() {
        try {
            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s",
                $this->driver,
                $this->host,
                $this->port,
                $this->dbname
            );

            $connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            error_log("Database connection successful");
            return $connection;

        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            error_log("Connection Details - Host: {$this->host}, DB: {$this->dbname}, Driver: {$this->driver}");
            throw new Exception("Database connection error: " . $e->getMessage());
        }
    }
}

// Example usage
try {
    $dbConfig = [
        'host' => 'your_host',
        'dbname' => 'your_database',
        'username' => 'your_username',
        'password' => 'your_password',
        'driver' => 'pgsql'  // or 'mysql'
    ];

    $db = new DatabaseConnection($dbConfig);
    $connection = $db->connect();

} catch (Exception $e) {
    die("Fatal Error: " . $e->getMessage());
}

// Clear output buffer
ob_end_clean();
?> 