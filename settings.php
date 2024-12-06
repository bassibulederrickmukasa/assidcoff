<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch current box prices
$stmt = $pdo->prepare("SELECT * FROM boxes");
$stmt->execute();
$boxes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Assidcoff Inventory</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Box Prices</h5>
                        <form id="priceForm">
                            <div class="mb-3">
                                <label class="form-label">Small Box Price (UGX)</label>
                                <input type="number" class="form-control" id="smallBoxPrice" value="300" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Big Box Price (UGX)</label>
                                <input type="number" class="form-control" id="bigBoxPrice" value="500" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Prices</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">System Backup</h5>
                        <button class="btn btn-success" onclick="backupDatabase()">Download Database Backup</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">System Information</h5>
                        <table class="table">
                            <tr>
                                <th>Database Size</th>
                                <td id="dbSize">Loading...</td>
                            </tr>
                            <tr>
                                <th>Total Records</th>
                                <td id="totalRecords">Loading...</td>
                            </tr>
                            <tr>
                                <th>Last Backup</th>
                                <td id="lastBackup">Loading...</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Change Admin Password</h5>
                        <form id="passwordForm">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="currentPassword" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/settings.js"></script>
</body>
</html> 