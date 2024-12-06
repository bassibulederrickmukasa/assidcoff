<?php
session_start();
require_once '../config/database.php';
require_once '../includes/security.php';

// Verify staff access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../index.php");
    exit();
}

// Get staff member's data
$stmt = $pdo->prepare("
    SELECT 
        s.name,
        (SELECT COALESCE(SUM(small_boxes), 0) FROM supplies WHERE staff_id = s.id AND date = CURRENT_DATE) as today_small_boxes,
        (SELECT COALESCE(SUM(big_boxes), 0) FROM supplies WHERE staff_id = s.id AND date = CURRENT_DATE) as today_big_boxes,
        (SELECT COALESCE(MAX(balance), 0) FROM payments WHERE staff_id = s.id) as current_balance
    FROM staff s
    WHERE s.id = (SELECT staff_id FROM users WHERE id = ?)
");
$stmt->execute([$_SESSION['user_id']]);
$staff_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - <?php echo SYSTEM_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Welcome, <?php echo htmlspecialchars($staff_data['name']); ?></h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Small Boxes</h5>
                        <h2><?php echo $staff_data['today_small_boxes']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Big Boxes</h5>
                        <h2><?php echo $staff_data['today_big_boxes']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Current Balance</h5>
                        <h2>UGX <?php echo number_format($staff_data['current_balance']); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Links</h5>
                        <div class="list-group">
                            <a href="view_supplies.php" class="list-group-item list-group-item-action">View My Supplies</a>
                            <a href="view_payments.php" class="list-group-item list-group-item-action">View My Payments</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Activity</h5>
                        <div id="recentActivity">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/staff-dashboard.js"></script>
</body>
</html>