<?php
require_once 'config/config.php';

// Move session configuration here
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SYSTEM_NAME; ?> - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2><?php echo COMPANY_NAME; ?> Inventory</h2>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php
                    switch ($_GET['error']) {
                        case 'invalid':
                            echo 'Invalid username or password';
                            break;
                        case 'timeout':
                            echo 'Session expired. Please login again';
                            break;
                        case 'system':
                            echo 'System error. Please try again later';
                            break;
                        default:
                            echo 'An error occurred. Please try again';
                    }
                    ?>
                </div>
            <?php endif; ?>
            <form action="auth/login.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required 
                           pattern="[a-zA-Z0-9_]+" title="Only letters, numbers and underscore allowed">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required 
                               minlength="<?php echo PASSWORD_MIN_LENGTH; ?>">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#togglePassword').click(function() {
                const passwordInput = $('#password');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                $(this).find('i').toggleClass('bi-eye bi-eye-slash');
            });

            // Basic form validation
            $('#loginForm').on('submit', function(e) {
                const username = $('#username').val().trim();
                const password = $('#password').val();

                if (username.length < 3) {
                    alert('Username must be at least 3 characters long');
                    e.preventDefault();
                    return;
                }

                if (password.length < <?php echo PASSWORD_MIN_LENGTH; ?>) {
                    alert('Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long');
                    e.preventDefault();
                    return;
                }
            });
        });
    </script>
</body>
</html>