<?php
// auth.php
// Authentication, session, and role helpers for Kismec Booking System

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Checks if a user is currently logged in.
 * If not, redirects them to the login page.
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Checks if the logged-in user is an administrator.
 * @return bool
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Enforces admin-only access. Redirects to main page if not an admin.
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        header('Location: index.php?error=unauthorized');
        exit;
    }
}

/**
 * API-specific authentication checks. Returns JSON errors instead of redirecting.
 */
function api_require_login() {
    if (!isset($_SESSION['user_id'])) {
        api_respond(401, 'Unauthorized access. Please log in.');
    }
}

/**
 * API-specific admin checks. Returns JSON errors.
 */
function api_require_admin() {
    api_require_login();
    if (!is_admin()) {
        api_respond(403, 'Forbidden. Administrator privileges required.');
    }
}

/**
 * Standardized JSON response helper for API endpoints.
 */
function api_respond($status_code, $message, $data = []) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => $status_code >= 200 && $status_code < 300 ? 'success' : 'error',
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
?>
