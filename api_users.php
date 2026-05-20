<?php
// api_users.php
// AJAX API endpoints for Administrator user account operations (Create, Reset Password, and Delete)

require_once 'db_config.php';
require_once 'auth.php';

// Enforce admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    api_respond(403, 'Forbidden. Administrator privileges required.');
}

$admin_user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // 1. ACTION: CREATE USER
    if ($action === 'create') {
        $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $role = isset($_POST['role']) ? trim($_POST['role']) : 'user';

        // Validations
        if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
            api_respond(400, 'Please complete all required fields.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            api_respond(400, 'Please provide a valid email address.');
        }

        if ($role !== 'user' && $role !== 'admin') {
            api_respond(400, 'Invalid system role selected.');
        }

        try {
            // Check if username is already taken
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                api_respond(400, "Username '$username' is already taken. Please choose another.");
            }

            // Check if email is already taken
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                api_respond(400, "Email address '$email' is already registered.");
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert into Database
            $insertStmt = $pdo->prepare("INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) 
                                         VALUES (:username, :password, :full_name, :email, :role)");
            $insertStmt->execute([
                'username' => $username,
                'password' => $hashedPassword,
                'full_name' => $fullName,
                'email' => $email,
                'role' => $role
            ]);

            api_respond(201, "Account for '$fullName' has been created successfully!");

        } catch (PDOException $e) {
            api_respond(500, 'Database error while registering user: ' . $e->getMessage());
        }
    }

    // 2. ACTION: RESET PASSWORD
    elseif ($action === 'reset_password') {
        $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $newPassword = isset($_POST['password']) ? trim($_POST['password']) : '';

        if ($userId <= 0 || empty($newPassword)) {
            api_respond(400, 'Please supply both a valid user ID and the new password.');
        }

        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();

            if (!$user) {
                api_respond(404, 'User account not found.');
            }

            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update password in DB
            $updateStmt = $pdo->prepare("UPDATE `users` SET `password` = :password WHERE `id` = :id");
            $updateStmt->execute([
                'password' => $hashedPassword,
                'id' => $userId
            ]);

            api_respond(200, "Password for '" . htmlspecialchars($user['full_name']) . "' has been reset successfully.");

        } catch (PDOException $e) {
            api_respond(500, 'Database error during password reset: ' . $e->getMessage());
        }
    }

    // 3. ACTION: DELETE USER
    elseif ($action === 'delete') {
        $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

        if ($userId <= 0) {
            api_respond(400, 'Invalid user ID.');
        }

        // Prevent self-deletion
        if ($userId === $admin_user_id) {
            api_respond(400, 'Deletion blocked. You cannot delete your own account while logged in.');
        }

        try {
            // Verify if user exists
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();

            if (!$user) {
                api_respond(404, 'User account not found.');
            }

            // Delete user
            $deleteStmt = $pdo->prepare("DELETE FROM `users` WHERE `id` = :id");
            $deleteStmt->execute(['id' => $userId]);

            api_respond(200, "Account for '" . htmlspecialchars($user['full_name']) . "' was deleted successfully.");

        } catch (PDOException $e) {
            api_respond(500, 'Database error during user deletion: ' . $e->getMessage());
        }
    }

    // Unknown action
    else {
        api_respond(400, 'Invalid API operation action.');
    }
} else {
    api_respond(405, 'HTTP Request Method Not Allowed.');
}
?>
