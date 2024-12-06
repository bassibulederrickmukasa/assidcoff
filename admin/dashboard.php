<?php
session_start();
require_once '../config/database.php';
require_once '../includes/security.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get summary data
$stmt = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM staff) as staff_count,
        (SELECT COALESCE(SUM(small_boxes), 0) FROM daily_production WHERE date = CURRENT_DATE) as today_small_boxes,
        (SELECT COALESCE(SUM(big_boxes), 0) FROM daily_production WHERE date = CURRENT_DATE) as today_big_boxes,
        (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE date = CURRENT_DATE) as today_payments
    FROM dual
");
$stmt->execute();
$summary = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SYSTEM_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Admin Dashboard</h2>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Staff</h5>
                        <h2><?php echo $summary['staff_count']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Small Boxes</h5>
                        <h2><?php echo $summary['today_small_boxes']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Big Boxes</h5>
                        <h2><?php echo $summary['today_big_boxes']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Payments</h5>
                        <h2>UGX <?php echo number_format($summary['today_payments']); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="list-group">
                            <a href="production.php" class="list-group-item list-group-item-action">Record Production</a>
                            <a href="supplies.php" class="list-group-item list-group-item-action">Record Supplies</a>
                            <a href="payments.php" class="list-group-item list-group-item-action">Record Payments</a>
                            <a href="reports.php" class="list-group-item list-group-item-action">View Reports</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Activities</h5>
                        <div id="recentActivities">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin-dashboard.js"></script>
</body>
</html>