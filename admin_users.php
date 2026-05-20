<?php
// admin_users.php
// User Management interface for Kismec Booking System Administrators

require_once 'db_config.php';
require_once 'auth.php';

// Enforce admin privileges
require_admin();

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

// Fetch all users to display in the grid
try {
    $stmt = $pdo->prepare("SELECT id, username, full_name, email, role, created_at FROM `users` ORDER BY `role` ASC, `full_name` ASC");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kismec Admin panel to manage system accounts and credentials.">
    <title>Manage Users - Kismec Booking System</title>
    <link rel="stylesheet" href="style.php">
</head>
<body>
    <!-- Mobile Navigation Header -->
    <header class="mobile-header">
        <div class="mobile-title">Kismec Admin</div>
        <button class="menu-toggle" id="btn-menu-toggle" aria-label="Toggle Navigation Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <div class="container-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="dashboard-sidebar">
            <div class="sidebar-logo">
                <span>Kismec Booking</span>
            </div>

            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    <?php echo strtoupper(substr($full_name, 0, 1)); ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name" title="<?php echo htmlspecialchars($full_name); ?>">
                        <?php echo htmlspecialchars($full_name); ?>
                    </div>
                    <div class="sidebar-user-role">
                        <span class="badge badge-admin">Admin</span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="index.php" class="sidebar-link" id="nav-dashboard">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Calendar view
                </a>
                <a href="admin_resources.php" class="sidebar-link" id="nav-resources">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Manage Rooms
                </a>
                <a href="admin_users.php" class="sidebar-link active" id="nav-users">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Manage Users
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="https://github.com/Cr45hOverride/ResourcesBookingSystem" target="_blank" rel="noopener noreferrer" class="sidebar-link" id="nav-github" style="margin-bottom: 8px;">
                    <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.167 6.839 9.49.5.092.682-.217.682-.48 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.464-1.11-1.464-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.577.688.479C19.138 20.164 22 16.418 22 12c0-5.523-4.477-10-10-10z" />
                    </svg>
                    GitHub Project
                </a>
                <a href="logout.php" class="sidebar-link" id="nav-logout" style="color: var(--danger);">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Sign Out
                </a>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="main-content">
            <div class="section-header">
                <div>
                    <h2 class="section-title" id="welcome-message">System Users</h2>
                    <p style="color: var(--text-secondary); font-size: 0.95rem;">Add Kismec staff, update login information, and reset passwords.</p>
                </div>
                <button class="btn btn-primary" id="btn-create-user">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Create User
                </button>
            </div>

            <!-- Toast / Global Actions Alerts -->
            <div id="users-alert-box" class="alert" style="display: none;"></div>

            <!-- Users Table Card -->
            <div class="glass-card" style="padding: 0; overflow: hidden;">
                <div class="table-responsive">
                    <table class="admin-table" id="users-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>System Role</th>
                                <th>Date Joined</th>
                                <th style="width: 260px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr data-id="<?php echo $u['id']; ?>">
                                    <td style="font-weight: 600; color: #ffffff;" class="td-fullname">
                                        <?php echo htmlspecialchars($u['full_name']); ?>
                                    </td>
                                    <td class="td-username"><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $u['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                            <?php echo ucfirst($u['role']); ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="actions-column">
                                            <button class="btn btn-secondary btn-small btn-reset-pwd" data-id="<?php echo $u['id']; ?>" data-name="<?php echo htmlspecialchars($u['full_name']); ?>">
                                                Reset Password
                                            </button>
                                            
                                            <?php if ($u['id'] !== $user_id): // Don't let users delete themselves ?>
                                                <button class="btn btn-danger btn-small btn-delete-user" data-id="<?php echo $u['id']; ?>">
                                                    Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Create User Modal -->
    <div class="modal-overlay" id="create-user-modal">
        <div class="modal-window">
            <div class="modal-header">
                <h4 class="modal-title">Create Kismec Account</h4>
                <button class="modal-close modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div id="create-user-alert" class="alert alert-error" style="display: none; padding: 10px 16px; font-size: 0.85rem; margin-bottom: 15px;"></div>
                
                <form id="create-user-form">
                    <div class="form-group">
                        <label for="new-fullname" class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="new-fullname" class="form-control" placeholder="e.g. John Doe" required>
                    </div>

                    <div class="form-group">
                        <label for="new-email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="new-email" class="form-control" placeholder="doe@kismec.org.my" required>
                    </div>

                    <div class="form-group">
                        <label for="new-username" class="form-label">Username</label>
                        <input type="text" name="username" id="new-username" class="form-control" placeholder="johndoe" required>
                    </div>

                    <div class="form-group">
                        <label for="new-password" class="form-label">Temporary Password</label>
                        <input type="password" name="password" id="new-password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="form-group">
                        <label for="new-role" class="form-label">Access Level</label>
                        <select name="role" id="new-role" class="form-control" required>
                            <option value="user" selected>User (Staff Booking Access)</option>
                            <option value="admin">Administrator (Full Access)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-submit-create-user">Create User</button>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal-overlay" id="reset-pwd-modal">
        <div class="modal-window">
            <div class="modal-header">
                <h4 class="modal-title">Reset User Password</h4>
                <button class="modal-close modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div id="reset-pwd-alert" class="alert alert-error" style="display: none; padding: 10px 16px; font-size: 0.85rem; margin-bottom: 15px;"></div>
                
                <h5 style="margin-bottom: 15px; font-size: 1rem; color: #ffffff;">
                    Resetting password for: <span id="reset-pwd-user-name" style="color: var(--primary);">John Doe</span>
                </h5>

                <form id="reset-pwd-form">
                    <input type="hidden" name="user_id" id="reset-pwd-userid" value="">
                    
                    <div class="form-group">
                        <label for="reset-new-password" class="form-label">New Password</label>
                        <input type="password" name="password" id="reset-new-password" class="form-control" placeholder="••••••••" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-submit-reset-pwd">Reset Password</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile sidebar toggle helper
            const btnMenuToggle = document.getElementById('btn-menu-toggle');
            const sidebar = document.getElementById('dashboard-sidebar');
            if (btnMenuToggle && sidebar) {
                btnMenuToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                    btnMenuToggle.classList.toggle('active');
                });
                document.addEventListener('click', function(e) {
                    if (sidebar.classList.contains('active') && !sidebar.contains(e.target) && e.target !== btnMenuToggle) {
                        sidebar.classList.remove('active');
                        btnMenuToggle.classList.remove('active');
                    }
                });
            }

            // Global elements and modals selectors
            const usersAlertBox = document.getElementById('users-alert-box');
            
            // 1. Create User Flow
            const createModal = document.getElementById('create-user-modal');
            const btnCreateUser = document.getElementById('btn-create-user');
            const createForm = document.getElementById('create-user-form');
            const btnSubmitCreateUser = document.getElementById('btn-submit-create-user');
            const createAlert = document.getElementById('create-user-alert');

            // 2. Reset Password Flow
            const resetModal = document.getElementById('reset-pwd-modal');
            const resetForm = document.getElementById('reset-pwd-form');
            const resetUserIdField = document.getElementById('reset-pwd-userid');
            const resetUserNameSpan = document.getElementById('reset-pwd-user-name');
            const btnSubmitResetPwd = document.getElementById('btn-submit-reset-pwd');
            const resetAlert = document.getElementById('reset-pwd-alert');

            // Global functions for showing toasts
            function showToast(message, type = 'success') {
                usersAlertBox.textContent = message;
                usersAlertBox.className = `alert alert-${type}`;
                usersAlertBox.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => {
                    usersAlertBox.style.display = 'none';
                }, 5000);
            }

            // Open/Close handlers
            btnCreateUser.addEventListener('click', () => {
                createForm.reset();
                createAlert.style.display = 'none';
                createModal.classList.add('active');
            });

            document.querySelectorAll('.modal-close-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
                });
            });

            // Close modals when clicking outside window
            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        overlay.classList.remove('active');
                    }
                });
            });

            // Action: Reset Password Button Clicked
            document.querySelectorAll('.btn-reset-pwd').forEach(btn => {
                btn.addEventListener('click', () => {
                    const userId = btn.getAttribute('data-id');
                    const userName = btn.getAttribute('data-name');
                    
                    resetForm.reset();
                    resetAlert.style.display = 'none';
                    resetUserIdField.value = userId;
                    resetUserNameSpan.textContent = userName;
                    resetModal.classList.add('active');
                });
            });

            // AJAX: Submit Create User
            btnSubmitCreateUser.addEventListener('click', function() {
                const formData = new FormData(createForm);
                formData.append('action', 'create');

                btnSubmitCreateUser.disabled = true;
                btnSubmitCreateUser.textContent = 'Creating...';

                fetch('api_users.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    btnSubmitCreateUser.disabled = false;
                    btnSubmitCreateUser.textContent = 'Create User';

                    if (res.status === 'success') {
                        createModal.classList.remove('active');
                        showToast(res.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        createAlert.textContent = res.message;
                        createAlert.style.display = 'block';
                    }
                })
                .catch(err => {
                    btnSubmitCreateUser.disabled = false;
                    btnSubmitCreateUser.textContent = 'Create User';
                    createAlert.textContent = 'A connection error occurred. Please try again.';
                    createAlert.style.display = 'block';
                });
            });

            // AJAX: Submit Reset Password
            btnSubmitResetPwd.addEventListener('click', function() {
                const formData = new FormData(resetForm);
                formData.append('action', 'reset_password');

                btnSubmitResetPwd.disabled = true;
                btnSubmitResetPwd.textContent = 'Resetting...';

                fetch('api_users.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    btnSubmitResetPwd.disabled = false;
                    btnSubmitResetPwd.textContent = 'Reset Password';

                    if (res.status === 'success') {
                        resetModal.classList.remove('active');
                        showToast(res.message, 'success');
                    } else {
                        resetAlert.textContent = res.message;
                        resetAlert.style.display = 'block';
                    }
                })
                .catch(err => {
                    btnSubmitResetPwd.disabled = false;
                    btnSubmitResetPwd.textContent = 'Reset Password';
                    resetAlert.textContent = 'A connection error occurred. Please try again.';
                    resetAlert.style.display = 'block';
                });
            });

            // AJAX: Delete User Account
            document.querySelectorAll('.btn-delete-user').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = btn.getAttribute('data-id');
                    
                    if (!confirm('Are you absolutely sure you want to delete this user account? All their active bookings will be automatically removed.')) {
                        return;
                    }

                    btn.disabled = true;
                    btn.textContent = 'Deleting...';

                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('user_id', userId);

                    fetch('api_users.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            const tr = document.querySelector(`tr[data-id="${userId}"]`);
                            if (tr) tr.remove();
                            showToast(res.message, 'success');
                        } else {
                            btn.disabled = false;
                            btn.textContent = 'Delete';
                            showToast(res.message, 'error');
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.textContent = 'Delete';
                        showToast('Connection error occurred during deletion.', 'error');
                    });
                });
            });
        });
    </script>
</body>
</html>
