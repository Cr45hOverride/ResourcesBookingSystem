<?php
// index.php
// Main Dashboard and Interactive Calendar view for Kismec Booking System

require_once 'db_config.php';
require_once 'auth.php';

// Enforce login
require_login();

// Retrieve user credentials
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Fetch resources for selectors
try {
    $stmt = $pdo->prepare("SELECT * FROM `resources` WHERE `status` = 'active' ORDER BY `type` DESC, `name` ASC");
    $stmt->execute();
    $activeResources = $stmt->fetchAll();

    // Fetch all resources (for admin info / indicators)
    $stmtAll = $pdo->prepare("SELECT * FROM `resources` ORDER BY `type` DESC, `name` ASC");
    $stmtAll->execute();
    $allResources = $stmtAll->fetchAll();

    // Stats calculations
    // 1. Labs count
    $stmtStats = $pdo->prepare("SELECT COUNT(*) FROM `resources` WHERE `type` = 'lab'");
    $stmtStats->execute();
    $labsCount = $stmtStats->fetchColumn();

    // 2. Classrooms count
    $stmtStats = $pdo->prepare("SELECT COUNT(*) FROM `resources` WHERE `type` = 'classroom'");
    $stmtStats->execute();
    $classroomsCount = $stmtStats->fetchColumn();

    // 3. Maintenance count
    $stmtStats = $pdo->prepare("SELECT COUNT(*) FROM `resources` WHERE `status` = 'maintenance'");
    $stmtStats->execute();
    $maintenanceCount = $stmtStats->fetchColumn();

    // Generate time options from 07:00 to 22:00 in 30-minute intervals
    $timeOptions = [];
    $start = new DateTime('07:00');
    $end = new DateTime('22:00');
    $interval = new DateInterval('PT30M');
    $period = new DatePeriod($start, $interval, $end->modify('+30 minutes'));
    foreach ($period as $time) {
        $formatted = $time->format('H:i');
        $display = $time->format('h:i A');
        $timeOptions[] = ['val' => $formatted, 'display' => $display];
    }

} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kismec Lab and Classroom interactive booking dashboard.">
    <title>Dashboard - Kismec Booking System</title>
    <link rel="stylesheet" href="style.php">
</head>
<body>
    <!-- Mobile Navigation Header -->
    <header class="mobile-header">
        <div class="mobile-title">Kismec Booking</div>
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
                <span id="logo-text">Kismec Booking</span>
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
                        <span class="badge <?php echo $role === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                            <?php echo ucfirst($role); ?>
                        </span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="index.php" class="sidebar-link active" id="nav-dashboard">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Calendar view
                </a>
                
                <?php if (is_admin()): ?>
                    <a href="admin_resources.php" class="sidebar-link" id="nav-resources">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Manage Rooms
                    </a>
                    <a href="admin_users.php" class="sidebar-link" id="nav-users">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Manage Users
                    </a>
                <?php endif; ?>
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

        <!-- Main Dashboard View -->
        <main class="main-content">
            <div class="section-header">
                <div>
                    <h2 class="section-title" id="welcome-message">Welcome back, <?php echo htmlspecialchars($full_name); ?></h2>
                    <p style="color: var(--text-secondary); font-size: 0.95rem;">Reserve resources and manage schedules from the central calendar.</p>
                </div>
                <button class="btn btn-primary" id="btn-quick-book">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    New Booking
                </button>
            </div>

            <!-- Stats Bar -->
            <div class="stats-container">
                <div class="stat-card">
                    <span class="stat-value" id="stat-labs-count"><?php echo $labsCount; ?></span>
                    <span class="stat-label">Computer Labs</span>
                </div>
                <div class="stat-card">
                    <span class="stat-value" id="stat-classrooms-count"><?php echo $classroomsCount; ?></span>
                    <span class="stat-label">Classrooms</span>
                </div>
                <div class="stat-card" style="border-color: <?php echo $maintenanceCount > 0 ? 'var(--warning)' : 'var(--border-color)'; ?>;">
                    <span class="stat-value" id="stat-maintenance-count" style="color: <?php echo $maintenanceCount > 0 ? 'var(--warning)' : '#ffffff'; ?>;">
                        <?php echo $maintenanceCount; ?>
                    </span>
                    <span class="stat-label">Under Maintenance</span>
                </div>
            </div>

            <!-- Calendar Display Card -->
            <div class="glass-card" style="padding: 24px;">
                <div class="calendar-controls">
                    <!-- Navigation -->
                    <div class="calendar-nav-buttons">
                        <button class="btn btn-secondary btn-small" id="btn-prev-month" title="Previous Month">
                            <svg style="width: 16px; height: 16px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <h3 class="calendar-title-text" id="calendar-month-year">May 2026</h3>
                        <button class="btn btn-secondary btn-small" id="btn-next-month" title="Next Month">
                            <svg style="width: 16px; height: 16px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="calendar-filters">
                        <div>
                            <select id="filter-type" class="form-control filter-select">
                                <option value="all">All Resource Types</option>
                                <option value="lab">Computer Labs</option>
                                <option value="classroom">Classrooms</option>
                            </select>
                        </div>
                        <div>
                            <select id="filter-resource" class="form-control filter-select">
                                <option value="all">All Rooms</option>
                                <?php foreach ($activeResources as $res): ?>
                                    <option value="<?php echo $res['id']; ?>" data-type="<?php echo $res['type']; ?>">
                                        <?php echo htmlspecialchars($res['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Calendar Grid Container -->
                <div class="calendar-container">
                    <div class="calendar-days-header">
                        <div class="calendar-day-label">Sunday</div>
                        <div class="calendar-day-label">Monday</div>
                        <div class="calendar-day-label">Tuesday</div>
                        <div class="calendar-day-label">Wednesday</div>
                        <div class="calendar-day-label">Thursday</div>
                        <div class="calendar-day-label">Friday</div>
                        <div class="calendar-day-label">Saturday</div>
                    </div>
                    <div class="calendar-grid" id="calendar-grid-cells">
                        <!-- Filled dynamically by JavaScript -->
                    </div>
                </div>

                <!-- Calendar Legend -->
                <div class="calendar-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: rgba(16, 185, 129, 0.2); border: 1px solid var(--accent-lab);"></span>
                        <span>Computer Lab</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: rgba(139, 92, 246, 0.2); border: 1px solid var(--accent-classroom);"></span>
                        <span>Classroom</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Booking Creation & Details Modal -->
    <div class="modal-overlay" id="booking-modal-overlay">
        <div class="modal-window">
            <div class="modal-header">
                <h4 class="modal-title" id="booking-modal-title">Book a Resource</h4>
                <button class="modal-close" id="btn-close-booking-modal" aria-label="Close Modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Status/Alert area inside Modal -->
                <div id="modal-alert" class="alert" style="display: none; padding: 10px 16px; font-size: 0.85rem; margin-bottom: 15px;"></div>

                <form id="booking-form">
                    <!-- Hidden fields for edit/delete states -->
                    <input type="hidden" name="booking_id" id="booking-id-field" value="">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;" id="booking-dates-container">
                        <div class="form-group">
                            <label for="booking-date" class="form-label">Start Date</label>
                            <input type="date" name="booking_date" id="booking-date" class="form-control" required>
                        </div>
                        <div class="form-group" id="end-date-group">
                            <label for="booking-end-date" class="form-label">End Date</label>
                            <input type="date" name="booking_end_date" id="booking-end-date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="booking-resource" class="form-label">Select Resource</label>
                        <select name="resource_id" id="booking-resource" class="form-control" required>
                            <option value="" disabled selected>Choose a lab or classroom...</option>
                            <?php foreach ($activeResources as $res): ?>
                                <option value="<?php echo $res['id']; ?>">
                                    <?php echo htmlspecialchars($res['name']); ?> (<?php echo $res['type'] === 'lab' ? 'Computer Lab' : 'Classroom'; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="booking-start" class="form-label">Start Time</label>
                            <select name="start_time" id="booking-start" class="form-control" required>
                                <option value="" disabled selected>Start...</option>
                                <?php foreach ($timeOptions as $t): ?>
                                    <option value="<?php echo $t['val']; ?>"><?php echo $t['display']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="booking-end" class="form-label">End Time</label>
                            <select name="end_time" id="booking-end" class="form-control" required>
                                <option value="" disabled selected>End...</option>
                                <?php foreach ($timeOptions as $t): ?>
                                    <option value="<?php echo $t['val']; ?>"><?php echo $t['display']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="booking-purpose" class="form-label">Purpose of Booking</label>
                        <input type="text" name="purpose" id="booking-purpose" class="form-control" placeholder="e.g. CAD Training Session" required>
                    </div>

                    <!-- Ownership Display Meta -->
                    <div id="booking-owner-display" style="display: none; padding: 10px 12px; border-radius: 6px; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-secondary); margin-top: 15px;">
                        Booked by: <span id="booking-owner-name" style="color: #ffffff; font-weight: 600;">-</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-cancel-booking">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-delete-booking" style="display: none;">Delete Booking</button>
                <button type="button" class="btn btn-primary" id="btn-submit-booking">Save Reservation</button>
            </div>
        </div>
    </div>

    <!-- Hidden session data for JS helper checks -->
    <script>
        window.KISMEC_SESSION = {
            userId: <?php echo intval($user_id); ?>,
            role: '<?php echo esc_javascript($role); ?>',
            fullName: '<?php echo esc_javascript($full_name); ?>'
        };

        <?php
        // JS safe escaper helper
        function esc_javascript($str) {
            return str_replace(["\\", "\"", "\r", "\n"], ["\\\\", "\\\"", "\\r", "\\n"], $str);
        }
        ?>
    </script>
    <script src="calendar.php"></script>
</body>
</html>
