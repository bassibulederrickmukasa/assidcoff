<?php
session_start();
require_once 'config/database.php';
require_once 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Only admins and managers can access this page
if ($_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Get managers list
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'manager'");
$stmt->execute();
$managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch box prices
$stmt = $pdo->query("SELECT box_type, price FROM boxes");
$box_prices = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$small_price = $box_prices['small'] ?? 300;
$big_price = $box_prices['big'] ?? 500;

// Calculate total boxes and amount for supplied boxes
$stmt = $pdo->prepare("
    SELECT 
        COALESCE(SUM(small_boxes), 0) as total_small_boxes,
        COALESCE(SUM(big_boxes), 0) as total_big_boxes,
        COALESCE(SUM(small_boxes * :small_price), 0) + COALESCE(SUM(big_boxes * :big_price), 0) as total_production_value
    FROM supplies
    WHERE DATE(created_at) >= CURRENT_DATE() - INTERVAL 30 DAY
");
$stmt->execute([
    'small_price' => $small_price,
    'big_price' => $big_price
]);
$production_totals = $stmt->fetch(PDO::FETCH_ASSOC);

// Get total payments made
$stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total_payments FROM payments");
$total_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total_payments'];

// Calculate outstanding balance
$outstanding_balance = $production_totals['total_production_value'] - $total_payments;

// Get managers for dropdown (if admin is logged in)
if ($_SESSION['role'] === 'admin') {
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'manager'");
    $stmt->execute();
    $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Management - <?php echo SYSTEM_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Record Payment Received from Manager</h5>
                        <form id="paymentForm">
                            <div class="mb-3">
                                <label for="manager" class="form-label">Manager/CEO</label>
                                <select class="form-control" id="manager" name="manager_id" required>
                                    <option value="">Select Manager</option>
                                    <?php foreach ($managers as $manager): ?>
                                        <option value="<?php echo htmlspecialchars($manager['id']); ?>">
                                            <?php echo htmlspecialchars($manager['username']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount Received (UGX)</label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       min="0" step="100" required>
                                <small class="form-text text-muted">
                                    Outstanding Balance: UGX <?php echo number_format($outstanding_balance); ?>
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                       value="<?php echo date('Y-m-d'); ?>" 
                                       max="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2" 
                                         placeholder="Optional notes about this payment"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Record Payment</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Production and Payment Summary</h5>
                        <table class="table">
                            <tr>
                                <td>Total Small Boxes</td>
                                <td><?php echo number_format($production_totals['total_small_boxes']); ?></td>
                                <td>UGX <?php echo number_format($production_totals['total_small_boxes'] * $small_price); ?></td>
                            </tr>
                            <tr>
                                <td>Total Big Boxes</td>
                                <td><?php echo number_format($production_totals['total_big_boxes']); ?></td>
                                <td>UGX <?php echo number_format($production_totals['total_big_boxes'] * $big_price); ?></td>
                            </tr>
                            <tr class="table-info">
                                <th>Total Production Value</th>
                                <td colspan="2">UGX <?php echo number_format($production_totals['total_production_value']); ?></td>
                            </tr>
                            <tr class="table-success">
                                <th>Total Payments Made</th>
                                <td colspan="2">UGX <?php echo number_format($total_payments); ?></td>
                            </tr>
                            <tr class="table-primary">
                                <th>Outstanding Balance</th>
                                <td colspan="2">UGX <?php echo number_format($outstanding_balance); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Payment History</h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" onclick="window.exportPayments('excel')">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button type="button" class="btn btn-danger" onclick="window.exportPayments('pdf')">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button type="button" class="btn btn-primary" onclick="window.exportPayments('word')">
                                    <i class="fas fa-file-word"></i> Word
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Manager</th>
                                        <th>Recorded By</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentHistory">
                                    <tr>
                                        <td colspan="4" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/payments.js"></script>
</body>
</html>