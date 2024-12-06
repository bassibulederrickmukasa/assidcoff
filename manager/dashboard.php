<?php
session_start();
require_once '../config/database.php';
require_once '../includes/security.php';

// Verify manager access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

// Get summary data
$stmt = $pdo->prepare("
    SELECT 
        (SELECT COALESCE(SUM(small_boxes), 0) FROM daily_production WHERE date = CURRENT_DATE) as today_small_boxes,
        (SELECT COALESCE(SUM(big_boxes), 0) FROM daily_production WHERE date = CURRENT_DATE) as today_big_boxes,
        (SELECT COUNT(*) FROM supplies WHERE date = CURRENT_DATE) as today_supplies
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
    <title>Manager Dashboard - <?php echo SYSTEM_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Manager Dashboard</h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Small Boxes</h5>
                        <h2><?php echo $summary['today_small_boxes']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Big Boxes</h5>
                        <h2><?php echo $summary['today_big_boxes']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Supplies</h5>
                        <h2><?php echo $summary['today_supplies']; ?></h2>
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
                            <a href="supplies.php" class="list-group-item list-group-item-action">View & Comment on Supplies</a>
                            <a href="production.php" class="list-group-item list-group-item-action">View Production</a>
                            <a href="payments.php" class="list-group-item list-group-item-action">View Payments</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Comments</h5>
                        <div id="recentComments">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/manager-dashboard.js"></script>
</body>
</html>