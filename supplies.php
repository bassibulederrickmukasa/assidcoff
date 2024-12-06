<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch staff members for dropdown
$stmt = $pdo->prepare("SELECT id, name FROM staff");
$stmt->execute();
$staff_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user role
$isManager = $_SESSION['role'] === 'manager';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplies Management - Assidcoff Inventory</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <?php if (!$isManager): ?>
        <!-- Admin/Staff View -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Record Supply</h5>
                        <form id="supplyForm">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="staff" class="form-label">Staff Member</label>
                                <select class="form-control" id="staff" name="staff_id" required>
                                    <option value="">Select Staff Member</option>
                                    <?php foreach ($staff_members as $staff): ?>
                                        <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="smallBoxes" class="form-label">Small Boxes</label>
                                <input type="number" class="form-control" id="smallBoxes" name="small_boxes" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="bigBoxes" class="form-label">Big Boxes</label>
                                <input type="number" class="form-control" id="bigBoxes" name="big_boxes" min="0" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Record Supply</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Today's Supplies</h5>
                        <div id="supplyList">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Manager View -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title"><i class="fas fa-truck-loading"></i> Supply Records</h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary" onclick="filterSupplies('today')">Today</button>
                                <button type="button" class="btn btn-outline-primary" onclick="filterSupplies('week')">This Week</button>
                                <button type="button" class="btn btn-outline-primary" onclick="filterSupplies('month')">This Month</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Staff</th>
                                        <th>Small Boxes</th>
                                        <th>Big Boxes</th>
                                        <th>Total Value</th>
                                        <th>Comments</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="supplyRecords">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading supplies...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-comments"></i> Recent Comments</h5>
                        <div id="recentComments">
                            Loading comments...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Comment Modal -->
        <div class="modal fade" id="addCommentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="commentForm">
                            <input type="hidden" id="supplyId" name="supply_id">
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitComment()">Add Comment</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if ($isManager): ?>
    <script>
    // Manager's JavaScript
    let currentFilter = 'today';

    function filterSupplies(filter) {
        currentFilter = filter;
        loadSupplies();
    }

    function loadSupplies() {
        fetch(`api/get_supplies.php?filter=${currentFilter}`)
            .then(response => response.json())
            .then(response => {
                if (!response.success) {
                    throw new Error(response.message || 'Failed to load supplies');
                }
                
                const supplies = response.data.supplies || [];
                const totals = response.data.totals || {};
                
                let html = supplies.map(supply => `
                    <tr>
                        <td>${supply.date}</td>
                        <td>${supply.staff_name || 'N/A'}</td>
                        <td>${supply.small_boxes}</td>
                        <td>${supply.big_boxes}</td>
                        <td>UGX ${supply.total_value}</td>
                        <td>${supply.comments ? `<div class="comments-container">${supply.comments}</div>` : 'No comments'}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="openCommentModal(${supply.id})">
                                <i class="fas fa-comment"></i> Comment
                            </button>
                        </td>
                    </tr>
                `).join('');

                // Add totals row
                html += `
                    <tr class="table-info">
                        <td><strong>Total</strong></td>
                        <td></td>
                        <td><strong>${totals.total_small_boxes || 0}</strong></td>
                        <td><strong>${totals.total_big_boxes || 0}</strong></td>
                        <td><strong>UGX ${totals.total_value || 0}</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                `;

                document.getElementById('supplyRecords').innerHTML = html || '<tr><td colspan="7" class="text-center">No supplies found</td></tr>';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('supplyRecords').innerHTML = '<tr><td colspan="7" class="text-center">Error loading supplies: ' + error.message + '</td></tr>';
            });
    }

    function loadRecentComments() {
        fetch('api/get_recent_comments.php')
            .then(response => response.json())
            .then(data => {
                const commentsHtml = data.map(comment => `
                    <div class="comment-item mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>${comment.staff_name}</strong>
                            <small class="text-muted">${comment.date}</small>
                        </div>
                        <p class="mb-1">${comment.comment}</p>
                        <small class="text-muted">Supply ID: ${comment.supply_id}</small>
                    </div>
                `).join('');
                document.getElementById('recentComments').innerHTML = commentsHtml || 'No recent comments';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('recentComments').innerHTML = 'Error loading comments';
            });
    }

    function openCommentModal(supplyId) {
        document.getElementById('supplyId').value = supplyId;
        new bootstrap.Modal(document.getElementById('addCommentModal')).show();
    }

    function submitComment() {
        const formData = new FormData(document.getElementById('commentForm'));
        
        fetch('api/add_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addCommentModal')).hide();
                document.getElementById('commentForm').reset();
                loadSupplies();
                loadRecentComments();
            } else {
                alert('Error adding comment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding comment');
        });
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', function() {
        loadSupplies();
        loadRecentComments();
    });
    </script>
    <?php else: ?>
    <script src="assets/js/supplies.js"></script>
    <?php endif; ?>
</body>
</html>