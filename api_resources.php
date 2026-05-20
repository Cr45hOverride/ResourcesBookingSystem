<?php
// api_resources.php
// AJAX API endpoints for Administrator room management operations (Create, Edit, Toggle Maintenance, and Delete)

require_once 'db_config.php';
require_once 'auth.php';

// Enforce admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    api_respond(403, 'Forbidden. Administrator privileges required.');
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // 1. ACTION: CREATE RESOURCE (ROOM)
    if ($action === 'create') {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        // Validations
        if (empty($name) || empty($type) || empty($description)) {
            api_respond(400, 'Please complete all required fields.');
        }

        if ($type !== 'lab' && $type !== 'classroom') {
            api_respond(400, 'Invalid resource type selected.');
        }

        try {
            // Check for duplicate name
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `resources` WHERE `name` = :name");
            $stmt->execute(['name' => $name]);
            if ($stmt->fetchColumn() > 0) {
                api_respond(400, "A room or lab named '$name' already exists.");
            }

            // Create resource
            $insertStmt = $pdo->prepare("INSERT INTO `resources` (`name`, `type`, `description`, `status`) 
                                         VALUES (:name, :type, :description, 'active')");
            $insertStmt->execute([
                'name' => $name,
                'type' => $type,
                'description' => $description
            ]);

            api_respond(201, "New resource '$name' has been added successfully.");

        } catch (PDOException $e) {
            api_respond(500, 'Database error while adding resource: ' . $e->getMessage());
        }
    }

    // 2. ACTION: EDIT RESOURCE DETAILS
    elseif ($action === 'edit') {
        $resourceId = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        if ($resourceId <= 0 || empty($name) || empty($type) || empty($description)) {
            api_respond(400, 'Please complete all required fields.');
        }

        if ($type !== 'lab' && $type !== 'classroom') {
            api_respond(400, 'Invalid resource type selected.');
        }

        try {
            // Verify resource exists
            $stmt = $pdo->prepare("SELECT * FROM `resources` WHERE `id` = :id LIMIT 1");
            $stmt->execute(['id' => $resourceId]);
            $resource = $stmt->fetch();

            if (!$resource) {
                api_respond(404, 'Room details not found.');
            }

            // Check duplicate name excluding itself
            $checkNameStmt = $pdo->prepare("SELECT COUNT(*) FROM `resources` WHERE `name` = :name AND `id` != :id");
            $checkNameStmt->execute(['name' => $name, 'id' => $resourceId]);
            if ($checkNameStmt->fetchColumn() > 0) {
                api_respond(400, "Another room or lab named '$name' already exists.");
            }

            // Update resource details
            $updateStmt = $pdo->prepare("UPDATE `resources` 
                                         SET `name` = :name, `type` = :type, `description` = :description 
                                         WHERE `id` = :id");
            $updateStmt->execute([
                'name' => $name,
                'type' => $type,
                'description' => $description,
                'id' => $resourceId
            ]);

            api_respond(200, "Details for '$name' updated successfully.");

        } catch (PDOException $e) {
            api_respond(500, 'Database error while modifying resource: ' . $e->getMessage());
        }
    }

    // 3. ACTION: TOGGLE STATUS (ACTIVE / MAINTENANCE STATE)
    elseif ($action === 'toggle_status') {
        $resourceId = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';

        if ($resourceId <= 0 || ($status !== 'active' && $status !== 'maintenance')) {
            api_respond(400, 'Invalid inputs for status modification.');
        }

        try {
            // Check if resource exists
            $stmt = $pdo->prepare("SELECT * FROM `resources` WHERE `id` = :id LIMIT 1");
            $stmt->execute(['id' => $resourceId]);
            $resource = $stmt->fetch();

            if (!$resource) {
                api_respond(404, 'Selected room not found.');
            }

            // Update status
            $updateStmt = $pdo->prepare("UPDATE `resources` SET `status` = :status WHERE `id` = :id");
            $updateStmt->execute([
                'status' => $status,
                'id' => $resourceId
            ]);

            $statusText = $status === 'active' ? 'activated' : 'placed under maintenance';
            api_respond(200, "Room '" . htmlspecialchars($resource['name']) . "' has been successfully $statusText.");

        } catch (PDOException $e) {
            api_respond(500, 'Database error during status transition: ' . $e->getMessage());
        }
    }

    // 4. ACTION: DELETE RESOURCE (ROOM)
    elseif ($action === 'delete') {
        $resourceId = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;

        if ($resourceId <= 0) {
            api_respond(400, 'Invalid resource ID.');
        }

        try {
            // Verify if resource exists
            $stmt = $pdo->prepare("SELECT * FROM `resources` WHERE `id` = :id LIMIT 1");
            $stmt->execute(['id' => $resourceId]);
            $resource = $stmt->fetch();

            if (!$resource) {
                api_respond(404, 'Room details not found.');
            }

            // Delete resource
            $deleteStmt = $pdo->prepare("DELETE FROM `resources` WHERE `id` = :id");
            $deleteStmt->execute(['id' => $resourceId]);

            api_respond(200, "Room '" . htmlspecialchars($resource['name']) . "' and all its bookings have been permanently deleted.");

        } catch (PDOException $e) {
            api_respond(500, 'Database error during resource deletion: ' . $e->getMessage());
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
