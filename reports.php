<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Assidcoff Inventory</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Generate Report</h5>
                        <form id="reportForm" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Report Type</label>
                                <select class="form-control" id="reportType" required>
                                    <option value="production">Production Report</option>
                                    <option value="supplies">Supplies Report</option>
                                    <option value="payments">Payments Report</option>
                                    <option value="staff">Staff Performance</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                                <button type="button" class="btn btn-success ms-2" id="exportBtn">Export to Excel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div id="reportResults"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/reports.js"></script>
</body>
</html> 