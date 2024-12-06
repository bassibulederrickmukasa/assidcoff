<?php
require_once 'config/config.php';

// Move session configuration here
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

session_start();
require_once 'config/database.php';
require_once 'includes/security.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Log dashboard access
logActivity($pdo, $_SESSION['user_id'], 'dashboard_access', 'User accessed dashboard');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SYSTEM_NAME; ?> - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="btn-group" role="group">
                            <a href="production.php" class="btn btn-primary">New Production</a>
                            <a href="supplies.php" class="btn btn-success">Record Supply</a>
                            <a href="payments.php" class="btn btn-info">Record Payment</a>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="reports.php" class="btn btn-warning">Generate Report</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Today's Production</h5>
                        <div class="dashboard-stats">
                            <p class="card-text">Small: <span id="smallBoxes">0</span></p>
                            <p class="card-text">Big: <span id="bigBoxes">0</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Current Stock</h5>
                        <div class="dashboard-stats">
                            <p class="card-text">Small: <span id="smallStock">0</span></p>
                            <p class="card-text">Big: <span id="bigStock">0</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Today's Supplies</h5>
                        <div class="dashboard-stats">
                            <p class="card-text">Small: <span id="smallSupplies">0</span></p>
                            <p class="card-text">Big: <span id="bigSupplies">0</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Today's Payments</h5>
                        <div class="dashboard-stats">
                            <p class="card-text">Amount: <?php echo CURRENCY; ?> <span id="todayPayments">0</span></p>
                            <p class="card-text">Balance: <?php echo CURRENCY; ?> <span id="currentBalance">0</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Production Trends</h5>
                        <canvas id="productionChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Payment Summary</h5>
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <!-- Admin Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">System Overview</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="admin-stat">
                                    <h6>Total Users</h6>
                                    <p id="totalUsers" class="dashboard-stats">Loading...</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="admin-stat">
                                    <h6>Total Revenue</h6>
                                    <p id="totalRevenue" class="dashboard-stats">Loading...</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="admin-stat">
                                    <h6>Active Staff</h6>
                                    <p id="activeStaff" class="dashboard-stats">Loading...</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="admin-stat">
                                    <h6>Last Backup</h6>
                                    <p id="lastBackup" class="dashboard-stats">Loading...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html> 