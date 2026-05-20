<?php
// admin_resources.php
// Lab & Classroom Management interface for Kismec Booking System Administrators

require_once 'db_config.php';
require_once 'auth.php';

// Enforce admin privileges
require_admin();

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$role = $_SESSION['role'];

// Fetch all resources
try {
    $stmt = $pdo->prepare("SELECT * FROM `resources` ORDER BY `type` DESC, `name` ASC");
    $stmt->execute();
    $resources = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kismec Admin panel to manage computer labs, classrooms, and maintenance states.">
    <title>Manage Rooms - Kismec Booking System</title>
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
                <a href="admin_resources.php" class="sidebar-link active" id="nav-resources">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Manage Rooms
                </a>
                <a href="admin_users.php" class="sidebar-link" id="nav-users">
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
                    <h2 class="section-title" id="welcome-message">System Rooms</h2>
                    <p style="color: var(--text-secondary); font-size: 0.95rem;">Add computer labs or classrooms, edit details, and toggle maintenance status.</p>
                </div>
                <button class="btn btn-primary" id="btn-add-resource">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Room
                </button>
            </div>

            <!-- Toast / Global Actions Alerts -->
            <div id="resources-alert-box" class="alert" style="display: none;"></div>

            <!-- Resources Table Card -->
            <div class="glass-card" style="padding: 0; overflow: hidden;">
                <div class="table-responsive">
                    <table class="admin-table" id="resources-table">
                        <thead>
                            <tr>
                                <th>Room Name</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th style="width: 320px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resources as $res): ?>
                                <tr data-id="<?php echo $res['id']; ?>">
                                    <td style="font-weight: 600; color: #ffffff;" class="td-name">
                                        <?php echo htmlspecialchars($res['name']); ?>
                                    </td>
                                    <td class="td-type" style="text-transform: capitalize;">
                                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                                            <span class="legend-color" style="width: 10px; height: 10px; display: inline-block; background-color: <?php echo $res['type'] === 'lab' ? 'var(--accent-lab)' : 'var(--accent-classroom)'; ?>;"></span>
                                            <?php echo $res['type'] === 'lab' ? 'Computer Lab' : 'Classroom'; ?>
                                        </span>
                                    </td>
                                    <td class="td-description" style="color: var(--text-secondary); font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($res['description']); ?>
                                    </td>
                                    <td>
                                        <span class="badge td-status-badge <?php echo $res['status'] === 'active' ? 'badge-active' : 'badge-maintenance'; ?>">
                                            <?php echo $res['status'] === 'active' ? 'Active' : 'Maintenance'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions-column">
                                            <button class="btn btn-secondary btn-small btn-toggle-maintenance" data-id="<?php echo $res['id']; ?>" data-status="<?php echo $res['status']; ?>">
                                                <?php echo $res['status'] === 'active' ? 'Put Maintenance' : 'Activate'; ?>
                                            </button>
                                            
                                            <button class="btn btn-secondary btn-small btn-edit-resource" 
                                                    data-id="<?php echo $res['id']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($res['name']); ?>"
                                                    data-type="<?php echo $res['type']; ?>"
                                                    data-desc="<?php echo htmlspecialchars($res['description']); ?>">
                                                Edit
                                            </button>

                                            <button class="btn btn-danger btn-small btn-delete-resource" data-id="<?php echo $res['id']; ?>">
                                                Delete
                                            </button>
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

    <!-- Add / Edit Resource Modal -->
    <div class="modal-overlay" id="resource-modal">
        <div class="modal-window">
            <div class="modal-header">
                <h4 class="modal-title" id="resource-modal-title">Add New Room</h4>
                <button class="modal-close modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div id="resource-modal-alert" class="alert alert-error" style="display: none; padding: 10px 16px; font-size: 0.85rem; margin-bottom: 15px;"></div>
                
                <form id="resource-form">
                    <input type="hidden" name="resource_id" id="resource-id-field" value="">
                    
                    <div class="form-group">
                        <label for="res-name" class="form-label">Room / Lab Name</label>
                        <input type="text" name="name" id="res-name" class="form-control" placeholder="e.g. Computer Lab 6 or Classroom F" required>
                    </div>

                    <div class="form-group">
                        <label for="res-type" class="form-label">Resource Type</label>
                        <select name="type" id="res-type" class="form-control" required>
                            <option value="lab">Computer Lab</option>
                            <option value="classroom">Classroom</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="res-desc" class="form-label">Description / Equipment Details</label>
                        <textarea name="description" id="res-desc" class="form-control" placeholder="e.g. Equipped with 30 computers, overhead projector, whiteboards." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-submit-resource">Save Room</button>
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

            // Global elements and modal selectors
            const resourcesAlertBox = document.getElementById('resources-alert-box');
            
            const resourceModal = document.getElementById('resource-modal');
            const resourceModalTitle = document.getElementById('resource-modal-title');
            const btnAddResource = document.getElementById('btn-add-resource');
            const resourceForm = document.getElementById('resource-form');
            const fieldResourceId = document.getElementById('resource-id-field');
            const fieldResName = document.getElementById('res-name');
            const fieldResType = document.getElementById('res-type');
            const fieldResDesc = document.getElementById('res-desc');
            const btnSubmitResource = document.getElementById('btn-submit-resource');
            const resourceAlert = document.getElementById('resource-modal-alert');

            // Show global toast
            function showToast(message, type = 'success') {
                resourcesAlertBox.textContent = message;
                resourcesAlertBox.className = `alert alert-${type}`;
                resourcesAlertBox.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => {
                    resourcesAlertBox.style.display = 'none';
                }, 5000);
            }

            // Open Modal for CREATE
            btnAddResource.addEventListener('click', () => {
                resourceForm.reset();
                fieldResourceId.value = '';
                resourceModalTitle.textContent = 'Add New Room';
                resourceAlert.style.display = 'none';
                resourceModal.classList.add('active');
            });

            // Close handlers
            document.querySelectorAll('.modal-close-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    resourceModal.classList.remove('active');
                });
            });

            resourceModal.addEventListener('click', function(e) {
                if (e.target === resourceModal) {
                    resourceModal.classList.remove('active');
                }
            });

            // Open Modal for EDIT
            document.querySelectorAll('.btn-edit-resource').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const name = btn.getAttribute('data-name');
                    const type = btn.getAttribute('data-type');
                    const desc = btn.getAttribute('data-desc');

                    fieldResourceId.value = id;
                    fieldResName.value = name;
                    fieldResType.value = type;
                    fieldResDesc.value = desc;

                    resourceModalTitle.textContent = 'Edit Room Details';
                    resourceAlert.style.display = 'none';
                    resourceModal.classList.add('active');
                });
            });

            // AJAX: Submit Add / Edit
            btnSubmitResource.addEventListener('click', function() {
                const isEdit = fieldResourceId.value !== '';
                const formData = new FormData(resourceForm);
                formData.append('action', isEdit ? 'edit' : 'create');

                btnSubmitResource.disabled = true;
                btnSubmitResource.textContent = 'Saving...';

                fetch('api_resources.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    btnSubmitResource.disabled = false;
                    btnSubmitResource.textContent = 'Save Room';

                    if (res.status === 'success') {
                        resourceModal.classList.remove('active');
                        showToast(res.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        resourceAlert.textContent = res.message;
                        resourceAlert.style.display = 'block';
                    }
                })
                .catch(err => {
                    btnSubmitResource.disabled = false;
                    btnSubmitResource.textContent = 'Save Room';
                    resourceAlert.textContent = 'A connection error occurred. Please try again.';
                    resourceAlert.style.display = 'block';
                });
            });

            // AJAX: Toggle Maintenance Mode Status
            document.querySelectorAll('.btn-toggle-maintenance').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = btn.getAttribute('data-id');
                    const currentStatus = btn.getAttribute('data-status');
                    const newStatus = currentStatus === 'active' ? 'maintenance' : 'active';
                    
                    const confirmMsg = currentStatus === 'active'
                        ? 'Put this room under maintenance? Future bookings will be blocked.'
                        : 'Re-activate this room? It will be immediately available for staff booking.';

                    if (!confirm(confirmMsg)) return;

                    btn.disabled = true;
                    btn.textContent = 'Updating...';

                    const formData = new FormData();
                    formData.append('action', 'toggle_status');
                    formData.append('resource_id', id);
                    formData.append('status', newStatus);

                    fetch('api_resources.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            showToast(res.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            btn.disabled = false;
                            btn.textContent = currentStatus === 'active' ? 'Put Maintenance' : 'Activate';
                            showToast(res.message, 'error');
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.textContent = currentStatus === 'active' ? 'Put Maintenance' : 'Activate';
                        showToast('Connection error occurred while updating status.', 'error');
                    });
                });
            });

            // AJAX: Delete Resource
            document.querySelectorAll('.btn-delete-resource').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = btn.getAttribute('data-id');
                    
                    if (!confirm('Are you absolutely sure you want to delete this room? ALL active and past bookings associated with it will be permanently deleted from the database!')) {
                        return;
                    }

                    btn.disabled = true;
                    btn.textContent = 'Deleting...';

                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('resource_id', id);

                    fetch('api_resources.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            const tr = document.querySelector(`tr[data-id="${id}"]`);
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
