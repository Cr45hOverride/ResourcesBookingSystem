<?php
// login.php
// User login page for Kismec Booking System

require_once 'db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = trim($_POST['username'] ?? '');
    $passwordInput = trim($_POST['password'] ?? '');

    if (empty($usernameInput) || empty($passwordInput)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            // Find the user by username
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `username` = :username LIMIT 1");
            $stmt->execute(['username' => $usernameInput]);
            $user = $stmt->fetch();

            // Verify password
            if ($user && password_verify($passwordInput, $user['password'])) {
                // Regenerate session ID for security (session fixation protection)
                session_regenerate_id(true);

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Redirect to main page
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure booking management system login page for computer labs and classrooms.">
    <title>Login - Kismec Booking System</title>
    <link rel="stylesheet" href="style.php">
</head>
<body class="login-wrapper">
    <div class="login-card glass-card">
        <div class="login-header">
            <h1 class="login-logo" id="main-title"><span>Kismec</span> Booking</h1>
            <p class="login-subtitle">Sign in to book computer labs and classrooms</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" id="login-alert-error" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" id="login-form">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" autocomplete="username" placeholder="e.g. staff" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" id="btn-login" style="width: 100%; margin-top: 10px;">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>
