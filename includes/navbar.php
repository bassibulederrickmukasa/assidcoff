<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$isAdmin = $_SESSION['role'] === 'admin';
$isManager = $_SESSION['role'] === 'manager';
$isStaff = $_SESSION['role'] === 'staff';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <?php echo COMPANY_NAME; ?> Inventory
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" 
                       href="dashboard.php">Dashboard</a>
                </li>
                
                <?php if ($isAdmin || $isManager): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'production.php' ? 'active' : ''; ?>" 
                       href="production.php">Production</a>
                </li>
                <?php endif; ?>

                <?php if (!$isStaff): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'supplies.php' ? 'active' : ''; ?>" 
                       href="supplies.php">Supplies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'payments.php' ? 'active' : ''; ?>" 
                       href="payments.php">Payments</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'view_supplies.php' ? 'active' : ''; ?>" 
                       href="view_supplies.php">View Supplies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'view_payments.php' ? 'active' : ''; ?>" 
                       href="view_payments.php">View Payments</a>
                </li>
                <?php endif; ?>

                <?php if ($isAdmin || $isManager): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" 
                       href="reports.php">Reports</a>
                </li>
                <?php endif; ?>

                <?php if ($isAdmin): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                        <li>
                            <a class="dropdown-item <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" 
                               href="users.php">Manage Users</a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" 
                               href="settings.php">Settings</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="backups.php">Database Backup</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                               data-bs-target="#changePasswordModal">Change Password</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="auth/logout.php">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" required 
                               minlength="<?php echo PASSWORD_MIN_LENGTH; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="changePassword()">Change Password</button>
            </div>
        </div>
    </div>
</div>

<script>
function changePassword() {
    const newPassword = $('#newPassword').val();
    const confirmPassword = $('#confirmPassword').val();

    if (newPassword !== confirmPassword) {
        alert('New passwords do not match!');
        return;
    }

    $.ajax({
        url: 'api/change_password.php',
        type: 'POST',
        data: {
            current_password: $('#currentPassword').val(),
            new_password: newPassword
        },
        success: function(response) {
            alert('Password changed successfully!');
            $('#changePasswordModal').modal('hide');
            $('#changePasswordForm')[0].reset();
        },
        error: function(xhr) {
            alert('Error changing password: ' + xhr.responseText);
        }
    });
}
</script>