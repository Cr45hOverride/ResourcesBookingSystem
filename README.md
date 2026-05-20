# 🗓️ Kismec Resources Booking System

A state-of-the-art, high-performance web application designed for booking and managing institution resources (Labs and Classrooms) with maximum efficiency. Featuring a premium, fluid glassmorphic UI, responsive layouts, and transactional range bookings.

Developed with care by **Kamal Hidayat** & **Harmoni**.

---

## ✨ Core Features

* **📅 Multi-Day Range Bookings**: Reserve resources over consecutive day intervals in a single seamless request.
* **🕒 Preset Time Selectors**: Replaced standard keyboard inputs with custom, beautiful 30-minute interval drop-down controls (`07:00 AM` to `10:00 PM`) for quick scheduling.
* **🔒 Transactional Operations**: Booking range creation is protected by strict transactional database queries (`PDO::beginTransaction`). In case of conflict on any date in the range, the transaction safely rolls back to keep database integrity pristine.
* **🛡️ Built-in Conflict Detection**: Intelligent backend checks confirm availability across all target dates, preventing double-bookings and alerting users of conflicting times.
* **🎨 Premium Glassmorphic Theme**: A modern Dark Mode UI using fluid CSS variables, Google Fonts (`Outfit`), and smooth micro-animations that deliver a high-end application feel.
* **🔐 Dynamic Access Levels**: Secure, cookie-based session control with dual-role privileges:
  * **Users**: View active reservations and schedule resource bookings.
  * **Admins**: Full control over room administration, resource states (Active / Maintenance), and user profile management.

---

## 🛠️ Technology Stack

* **Frontend**: HTML5, Vanilla HSL CSS (No bulky frameworks), JavaScript (served with strict MIME-type headers).
* **Backend**: Object-Oriented PHP 8.x.
* **Database**: MySQL / MariaDB via secure PDO abstraction.
* **Architecture**: AJAX-powered asynchronous database communication.

---

## 👥 Developers & Contributors

This project is built and maintained by:
* **Kamal Hidayat** — *Lead Full-Stack Architect*
* **Harmoni** — *Lead UI/UX & Systems Developer*

---

## 🚀 Setup & Installation

Follow these steps to set up the booking system on a local environment using XAMPP (or any PHP/MySQL server):

### 1. Place the Source Files
Copy the `KismecBookingSystem` folder to your XAMPP document root:
```bash
c:\xampp\htdocs\KismecBookingSystem
```

### 2. Configure Database Connection (Optional)
If your local MySQL environment has a custom port or password, edit [db_config.php](file:///c:/xampp/htdocs/KismecBookingSystem/db_config.php):
```php
$host = 'localhost';
$username = 'root';
$password = ''; // Your local MySQL password
$dbname = 'KismecBookingSystem';
```

### 3. Initialize the Database
Start your XAMPP Control Panel and ensure **Apache** and **MySQL** are running. You can set up the database using either of the following two methods:

#### Method A: Automatic Seeding Script (Fastest)
Open your browser and navigate to:
```http
http://localhost/KismecBookingSystem/setup.php
```
This script will automatically connect to your MySQL server, create the `KismecBookingSystem` database, generate all tables, and seed the default accounts and rooms.

#### Method B: Manual SQL Database Import
If you prefer to manually import the database structure and seeds:
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) or your favorite MySQL client.
2. Go to the **Import** tab.
3. Click **Choose File** and select the [BookingSystem.sql](file:///c:/xampp/htdocs/KismecBookingSystem/BookingSystem.sql) file from the project root.
4. Click **Import** (or **Go**).
*(Alternatively, from your terminal/command line, run: `mysql -u root -p < BookingSystem.sql`)*

---

### 4. Default Seed Accounts
Once the database is initialized, log in to the application at `http://localhost/KismecBookingSystem/login.php` using the seeded accounts:

| Role | Username | Password | Full Name |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | System Administrator |
| **Standard User** | `staff` | `staff123` | Kismec Staff Member |

---

## 📁 Repository Structure

```
c:\xampp\htdocs\KismecBookingSystem
├── BookingSystem.sql     # Database schema and seed import script
├── index.php             # Core Booking Calendar View
├── login.php             # Modern Glassmorphic Login Screen
├── logout.php            # Active Session Terminating Handler
├── auth.php              # Session validation & security gateway
├── db_config.php         # PDO database configuration file
├── setup.php             # Automatic database seeding engine
├── admin_resources.php   # Admin Resource Management Layout
├── admin_users.php       # Admin User Management Layout
├── style.php             # Dynamic glassmorphic stylesheets
├── calendar.php          # Async AJAX controller and Modal handling
├── api_bookings.php      # Transactional reservation APIs
├── api_resources.php     # Resource administration APIs
└── api_users.php         # User management REST APIs
```
