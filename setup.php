<?php
// setup.php
// Database structure setup and seeding tool for Kismec Booking System

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'KismecBookingSystem';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Kismec Booking System</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --text-color: #c9d1d9;
            --primary: #58a6ff;
            --success: #2ea44f;
            --error: #f85149;
            --border-color: #30363d;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            text-align: center;
        }
        h1 {
            color: #ffffff;
            margin-bottom: 24px;
            font-size: 2rem;
        }
        .status-box {
            background-color: #0d1117;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 20px;
            text-align: left;
            font-family: monospace;
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 24px;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .status-item {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .status-success { color: var(--success); }
        .status-error { color: var(--error); }
        .status-info { color: var(--primary); }
        .btn {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: opacity 0.2s ease;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kismec Booking System Setup</h1>
        <div class="status-box">
            <?php
            function logStatus($msg, $type = 'info') {
                $class = 'status-info';
                $prefix = '[INFO]';
                if ($type === 'success') {
                    $class = 'status-success';
                    $prefix = '[OK]';
                } elseif ($type === 'error') {
                    $class = 'status-error';
                    $prefix = '[FAIL]';
                }
                echo "<div class='status-item $class'>$prefix " . htmlspecialchars($msg) . "</div>";
                flush();
            }

            try {
                // 1. Connect to MySQL Server without db name to create db if not exists
                logStatus("Connecting to MySQL at localhost...");
                $conn = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                logStatus("MySQL Server connected successfully.", "success");

                // 2. Create database
                logStatus("Creating Database '$dbname' if not exists...");
                $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                logStatus("Database created or verified.", "success");

                // 3. Connect to database
                $conn->exec("USE `$dbname`");
                logStatus("Switched context to '$dbname'.", "success");

                // 4. Create Users Table
                logStatus("Creating table 'users'...");
                $createUsersTable = "CREATE TABLE IF NOT EXISTS `users` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `username` VARCHAR(50) NOT NULL UNIQUE,
                    `password` VARCHAR(255) NOT NULL,
                    `full_name` VARCHAR(100) NOT NULL,
                    `email` VARCHAR(100) NOT NULL UNIQUE,
                    `role` ENUM('admin', 'user') DEFAULT 'user',
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                $conn->exec($createUsersTable);
                logStatus("Table 'users' created/verified.", "success");

                // 5. Create Resources Table (Labs and Classrooms)
                logStatus("Creating table 'resources'...");
                $createResourcesTable = "CREATE TABLE IF NOT EXISTS `resources` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) NOT NULL,
                    `type` ENUM('lab', 'classroom') NOT NULL,
                    `status` ENUM('active', 'maintenance') DEFAULT 'active',
                    `description` TEXT,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                $conn->exec($createResourcesTable);
                logStatus("Table 'resources' created/verified.", "success");

                // 6. Create Bookings Table
                logStatus("Creating table 'bookings'...");
                $createBookingsTable = "CREATE TABLE IF NOT EXISTS `bookings` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL,
                    `resource_id` INT NOT NULL,
                    `booking_date` DATE NOT NULL,
                    `start_time` TIME NOT NULL,
                    `end_time` TIME NOT NULL,
                    `purpose` VARCHAR(255) NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                    FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE,
                    INDEX (`booking_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                $conn->exec($createBookingsTable);
                logStatus("Table 'bookings' created/verified.", "success");

                // 7. Seed Admin & User Account if not exists
                logStatus("Checking default users...");
                
                // Seed Admin
                $stmt = $conn->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = 'admin'");
                $stmt->execute();
                if ($stmt->fetchColumn() == 0) {
                    logStatus("Seeding default admin user...");
                    $adminPwd = password_hash('admin123', PASSWORD_BCRYPT);
                    $seedAdmin = $conn->prepare("INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES ('admin', :pwd, 'System Administrator', 'admin@kismec.org.my', 'admin')");
                    $seedAdmin->execute(['pwd' => $adminPwd]);
                    logStatus("Admin account created (username: admin, password: admin123).", "success");
                } else {
                    logStatus("Admin account already exists.");
                }

                // Seed Default User (Staff)
                $stmt = $conn->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = 'staff'");
                $stmt->execute();
                if ($stmt->fetchColumn() == 0) {
                    logStatus("Seeding default staff user...");
                    $staffPwd = password_hash('staff123', PASSWORD_BCRYPT);
                    $seedStaff = $conn->prepare("INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES ('staff', :pwd, 'Kismec Staff Member', 'staff@kismec.org.my', 'user')");
                    $seedStaff->execute(['pwd' => $staffPwd]);
                    logStatus("Staff account created (username: staff, password: staff123).", "success");
                } else {
                    logStatus("Staff account already exists.");
                }

                // 8. Seed 5 Computer Labs and 5 Classrooms if table is empty
                logStatus("Checking resources data...");
                $stmt = $conn->prepare("SELECT COUNT(*) FROM `resources`");
                $stmt->execute();
                if ($stmt->fetchColumn() == 0) {
                    logStatus("Seeding 5 computer labs and 5 classrooms...");
                    
                    $resources = [
                        ['name' => 'Computer Lab 1', 'type' => 'lab', 'desc' => 'High-performance workstations with CAD software.'],
                        ['name' => 'Computer Lab 2', 'type' => 'lab', 'desc' => 'General purpose computer training lab.'],
                        ['name' => 'Computer Lab 3', 'type' => 'lab', 'desc' => 'Programming and database software setup.'],
                        ['name' => 'Computer Lab 4', 'type' => 'lab', 'desc' => 'Networking and security simulations lab.'],
                        ['name' => 'Computer Lab 5', 'type' => 'lab', 'desc' => 'Advanced AI and systems engineering lab.'],
                        
                        ['name' => 'Classroom A', 'type' => 'classroom', 'desc' => 'Capacity 40, equipped with smart projector.'],
                        ['name' => 'Classroom B', 'type' => 'classroom', 'desc' => 'Capacity 40, whiteboards and sound system.'],
                        ['name' => 'Classroom C', 'type' => 'classroom', 'desc' => 'Capacity 30, flexible seating layout.'],
                        ['name' => 'Classroom D', 'type' => 'classroom', 'desc' => 'Capacity 50, auditorial layout.'],
                        ['name' => 'Classroom E', 'type' => 'classroom', 'desc' => 'Capacity 25, ideal for discussions & seminars.']
                    ];

                    $insertRes = $conn->prepare("INSERT INTO `resources` (`name`, `type`, `description`, `status`) VALUES (:name, :type, :desc, 'active')");
                    foreach ($resources as $res) {
                        $insertRes->execute([
                            'name' => $res['name'],
                            'type' => $res['type'],
                            'desc' => $res['desc']
                        ]);
                    }
                    logStatus("Seeded 5 Labs and 5 Classrooms successfully.", "success");
                } else {
                    logStatus("Resources already seeded.");
                }

                logStatus("Database initialization successfully completed!", "success");
                echo "<p style='color: var(--success); font-weight: bold; margin-top: 15px;'>Your Kismec Booking System is ready to run!</p>";

            } catch (PDOException $e) {
                logStatus("Installation stopped due to database error.", "error");
                echo "<p style='color: var(--error); font-weight: bold;'>Error details: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>
        <a href="login.php" class="btn">Go to Login</a>
    </div>
</body>
</html>
