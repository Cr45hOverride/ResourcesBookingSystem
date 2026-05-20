<?php
// api_bookings.php
// AJAX endpoints for booking operations: Fetching calendar events, booking creations, modifications, and deletions

require_once 'db_config.php';
require_once 'auth.php';

// Enforce login for all API calls
if (!isset($_SESSION['user_id'])) {
    api_respond(401, 'Unauthorized. Please log in first.');
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET: Fetch bookings for calendar rendering
if ($method === 'GET') {
    $month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
    $year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
    
    $filterType = isset($_GET['type']) ? $_GET['type'] : 'all';
    $filterResource = isset($_GET['resource_id']) ? $_GET['resource_id'] : 'all';

    // Format start and end bounds for the month
    $startDate = sprintf('%04d-%02d-01', $year, $month);
    $endDate = date('Y-m-t', strtotime($startDate));

    try {
        $query = "SELECT b.*, u.full_name as booked_by, u.username as booked_by_username, r.name as resource_name, r.type as resource_type, r.status as resource_status
                  FROM `bookings` b
                  JOIN `users` u ON b.user_id = u.id
                  JOIN `resources` r ON b.resource_id = r.id
                  WHERE b.booking_date BETWEEN :start_date AND :end_date";
        
        $params = [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        // Apply filters
        if ($filterType !== 'all') {
            $query .= " AND r.type = :type";
            $params['type'] = $filterType;
        }

        if ($filterResource !== 'all') {
            $query .= " AND b.resource_id = :resource_id";
            $params['resource_id'] = intval($filterResource);
        }

        $query .= " ORDER BY b.booking_date ASC, b.start_time ASC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();

        api_respond(200, 'Bookings loaded successfully', $bookings);

    } catch (PDOException $e) {
        api_respond(500, 'Database error while fetching bookings: ' . $e->getMessage());
    }
}

// Handle POST: Create, Edit, or Delete booking
if ($method === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : 'create';

    // 1. DELETE Action
    if ($action === 'delete') {
        $bookingId = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;

        if ($bookingId <= 0) {
            api_respond(400, 'Invalid booking ID.');
        }

        try {
            // Check if booking exists
            $stmt = $pdo->prepare("SELECT * FROM `bookings` WHERE `id` = :id LIMIT 1");
            $stmt->execute(['id' => $bookingId]);
            $booking = $stmt->fetch();

            if (!$booking) {
                api_respond(404, 'Booking not found.');
            }

            // Permissions check: User can only delete their own booking unless they are an admin
            if ($booking['user_id'] !== $user_id && $role !== 'admin') {
                api_respond(403, 'Permission denied. You can only delete your own bookings.');
            }

            // Execute delete
            $deleteStmt = $pdo->prepare("DELETE FROM `bookings` WHERE `id` = :id");
            $deleteStmt->execute(['id' => $bookingId]);

            api_respond(200, 'Booking deleted successfully.');

        } catch (PDOException $e) {
            api_respond(500, 'Database error during deletion: ' . $e->getMessage());
        }
    }

    // 2. CREATE or EDIT Action
    if ($action === 'create' || $action === 'edit') {
        $bookingId = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
        $bookingDate = isset($_POST['booking_date']) ? trim($_POST['booking_date']) : '';
        $bookingEndDate = isset($_POST['booking_end_date']) ? trim($_POST['booking_end_date']) : '';
        $resourceId = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
        $startTime = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
        $endTime = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';
        $purpose = isset($_POST['purpose']) ? trim($_POST['purpose']) : '';

        // Basic inputs validation
        if (empty($bookingDate) || $resourceId <= 0 || empty($startTime) || empty($endTime) || empty($purpose)) {
            api_respond(400, 'Please fill in all required fields.');
        }

        // Default end date to start date if range was not selected or left empty
        if (empty($bookingEndDate) || $action === 'edit') {
            $bookingEndDate = $bookingDate;
        }

        // Validate time format and chronological ordering
        $startSeconds = strtotime("1970-01-01 $startTime");
        $endSeconds = strtotime("1970-01-01 $endTime");

        if ($startSeconds === false || $endSeconds === false) {
            api_respond(400, 'Invalid time format. Please select valid hours.');
        }

        if ($startSeconds >= $endSeconds) {
            api_respond(400, 'Start time must be strictly before end time.');
        }

        // Validate dates and construct targets range array
        $startStamp = strtotime($bookingDate);
        $endStamp = strtotime($bookingEndDate);
        if ($startStamp === false || $endStamp === false) {
            api_respond(400, 'Invalid date format.');
        }
        if ($startStamp > $endStamp) {
            api_respond(400, 'Start date must be on or before the end date.');
        }

        $datesToBook = [];
        $currentStamp = $startStamp;
        while ($currentStamp <= $endStamp) {
            $datesToBook[] = date('Y-m-d', $currentStamp);
            $currentStamp = strtotime('+1 day', $currentStamp);
        }

        if (count($datesToBook) > 31) {
            api_respond(400, 'Range booking is limited to a maximum of 31 consecutive days.');
        }

        try {
            // Check if resource exists and is active (not in maintenance)
            $resStmt = $pdo->prepare("SELECT * FROM `resources` WHERE `id` = :id LIMIT 1");
            $resStmt->execute(['id' => $resourceId]);
            $resource = $resStmt->fetch();

            if (!$resource) {
                api_respond(404, 'Selected resource not found.');
            }

            if ($resource['status'] === 'maintenance') {
                api_respond(400, 'Booking rejected. This resource is currently closed for maintenance.');
            }

            // If EDIT, check if the booking exists and verify permissions
            if ($action === 'edit') {
                if ($bookingId <= 0) {
                    api_respond(400, 'Invalid booking ID for editing.');
                }

                $stmt = $pdo->prepare("SELECT * FROM `bookings` WHERE `id` = :id LIMIT 1");
                $stmt->execute(['id' => $bookingId]);
                $existingBooking = $stmt->fetch();

                if (!$existingBooking) {
                    api_respond(404, 'Booking not found.');
                }

                // Check permissions
                if ($existingBooking['user_id'] !== $user_id && $role !== 'admin') {
                    api_respond(403, 'Permission denied. You can only edit your own bookings.');
                }
            }

            // Check overlaps for target dates
            if ($action === 'edit') {
                $overlapStmt = $pdo->prepare("SELECT COUNT(*) FROM `bookings` 
                                             WHERE `resource_id` = :resource_id 
                                             AND `booking_date` = :booking_date 
                                             AND (:start_time < `end_time` AND :end_time > `start_time`)
                                             AND `id` != :booking_id");
                $overlapStmt->execute([
                    'resource_id' => $resourceId,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'booking_id' => $bookingId
                ]);
                if ($overlapStmt->fetchColumn() > 0) {
                    api_respond(400, 'Booking collision detected! The selected time slot is already booked for this room.');
                }
            } else {
                // Create Range: verify all dates in target list are free
                $overlapStmt = $pdo->prepare("SELECT COUNT(*) FROM `bookings` 
                                             WHERE `resource_id` = :resource_id 
                                             AND `booking_date` = :booking_date 
                                             AND (:start_time < `end_time` AND :end_time > `start_time`)");
                foreach ($datesToBook as $date) {
                    $overlapStmt->execute([
                        'resource_id' => $resourceId,
                        'booking_date' => $date,
                        'start_time' => $startTime,
                        'end_time' => $endTime
                    ]);
                    if ($overlapStmt->fetchColumn() > 0) {
                        api_respond(400, "Booking collision detected on " . date('d M Y', strtotime($date)) . "! The selected time slot is already booked for this room.");
                    }
                }
            }

            // Proceed with database writes
            if ($action === 'create') {
                // Bulk write all dates inside a secure PDO transaction
                $pdo->beginTransaction();
                try {
                    $insertStmt = $pdo->prepare("INSERT INTO `bookings` (`user_id`, `resource_id`, `booking_date`, `start_time`, `end_time`, `purpose`) 
                                                 VALUES (:user_id, :resource_id, :booking_date, :start_time, :end_time, :purpose)");
                    foreach ($datesToBook as $date) {
                        $insertStmt->execute([
                            'user_id' => $user_id,
                            'resource_id' => $resourceId,
                            'booking_date' => $date,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'purpose' => $purpose
                        ]);
                    }
                    $pdo->commit();
                    api_respond(201, 'Booking created successfully.');
                } catch (Exception $txEx) {
                    $pdo->rollBack();
                    api_respond(500, 'Transaction failure: ' . $txEx->getMessage());
                }
            } else {
                // Edit / Update single day booking
                $updateStmt = $pdo->prepare("UPDATE `bookings` 
                                             SET `resource_id` = :resource_id, 
                                                 `booking_date` = :booking_date, 
                                                 `start_time` = :start_time, 
                                                 `end_time` = :end_time, 
                                                 `purpose` = :purpose 
                                             WHERE `id` = :booking_id");
                $updateStmt->execute([
                    'resource_id' => $resourceId,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'purpose' => $purpose,
                    'booking_id' => $bookingId
                ]);

                api_respond(200, 'Booking updated successfully.');
            }

        } catch (PDOException $e) {
            api_respond(500, 'Database query error: ' . $e->getMessage());
        }
    }
}
?>
