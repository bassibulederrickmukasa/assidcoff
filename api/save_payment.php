<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Add missing columns if they don't exist
try {
    $pdo->exec("
        ALTER TABLE payments 
        ADD COLUMN IF NOT EXISTS payment_date DATE NOT NULL AFTER id,
        ADD COLUMN IF NOT EXISTS manager_id INT NOT NULL AFTER payment_date,
        ADD COLUMN IF NOT EXISTS recorded_by INT NOT NULL AFTER manager_id,
        ADD COLUMN IF NOT EXISTS notes TEXT NULL AFTER amount,
        ADD CONSTRAINT fk_manager FOREIGN KEY (manager_id) REFERENCES users(id),
        ADD CONSTRAINT fk_recorded_by FOREIGN KEY (recorded_by) REFERENCES users(id)
    ");
} catch (PDOException $e) {
    error_log("Error updating table structure: " . $e->getMessage());
}

// Debug: Log all received data
error_log("POST Data: " . print_r($_POST, true));
error_log("Raw Input: " . file_get_contents('php://input'));

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Only admins can record payments
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Only administrators can record payments']);
    exit();
}

// Debug: Log the specific fields we're looking for
error_log("manager_id: " . (isset($_POST['manager_id']) ? $_POST['manager_id'] : 'not set'));
error_log("amount: " . (isset($_POST['amount']) ? $_POST['amount'] : 'not set'));
error_log("payment_date: " . (isset($_POST['payment_date']) ? $_POST['payment_date'] : 'not set'));

// Validate required fields
$required_fields = ['manager_id', 'amount', 'payment_date'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => "Missing required field: $field",
            'received_data' => $_POST
        ]);
        exit();
    }
}

try {
    // Validate manager exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'manager'");
    $stmt->execute([$_POST['manager_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid manager selected');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Insert payment record
    $stmt = $pdo->prepare("
        INSERT INTO payments (
            payment_date,
            manager_id,
            recorded_by,
            amount,
            notes,
            created_at
        ) VALUES (
            ?, ?, ?, ?, ?, NOW()
        )
    ");

    $stmt->execute([
        $_POST['payment_date'],
        $_POST['manager_id'],
        $_SESSION['user_id'],  // recorded_by is the current admin
        $_POST['amount'],
        $_POST['notes'] ?? null
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Payment recorded successfully'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in save_payment.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to record payment: ' . $e->getMessage()
    ]);
}