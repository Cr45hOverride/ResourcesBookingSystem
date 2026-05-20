<?php
header("Content-Type: text/css; charset=utf-8");
?>
/* style.php */
/* Modern design system and premium responsive stylesheets for Kismec Booking System */

@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

:root {
    --bg-primary: #080c14;
    --bg-secondary: #0f1524;
    --bg-tertiary: #151e33;
    --border-color: #1f2c4a;
    --text-primary: #f1f5f9;
    --text-secondary: #94a3b8;
    --text-muted: #64748b;
    
    --primary: #6366f1;
    --primary-hover: #4f46e5;
    --primary-glow: rgba(99, 102, 241, 0.35);
    
    --accent-lab: #10b981;
    --accent-lab-glow: rgba(16, 185, 129, 0.2);
    --accent-classroom: #8b5cf6;
    --accent-classroom-glow: rgba(139, 92, 246, 0.2);
    
    --danger: #ef4444;
    --danger-hover: #dc2626;
    --danger-glow: rgba(239, 68, 68, 0.2);
    
    --warning: #f59e0b;
    --warning-glow: rgba(245, 158, 11, 0.2);
    --success: #10b981;
    
    --transition-fast: all 0.15s ease;
    --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    
    --font: 'Outfit', sans-serif;
    --glass-bg: rgba(15, 21, 36, 0.7);
    --glass-border: rgba(255, 255, 255, 0.05);
    --shadow-premium: 0 10px 30px -10px rgba(0, 0, 0, 0.7);
    
    --sidebar-width: 260px;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: var(--font);
    background-color: var(--bg-primary);
    color: var(--text-primary);
    line-height: 1.5;
    overflow-x: hidden;
    background-image: 
        radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.05) 0px, transparent 50%);
    background-attachment: fixed;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
::-webkit-scrollbar-track {
    background: var(--bg-primary);
}
::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}

/* ==========================================================================
   Utility Classes & Layout
   ========================================================================== */
.container-layout {
    display: flex;
    min-height: 100vh;
}

.main-content {
    flex: 1;
    padding: 40px;
    margin-left: var(--sidebar-width);
    transition: var(--transition-smooth);
    min-width: 0; /* Prevents flex items from breaking grid widths */
}

/* Glass Card */
.glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    box-shadow: var(--shadow-premium);
    padding: 30px;
    margin-bottom: 30px;
    transition: var(--transition-smooth);
}
.glass-card:hover {
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.8);
}

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.5px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: var(--font);
    font-size: 0.95rem;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: var(--transition-fast);
}
.btn-primary {
    background-color: var(--primary);
    color: #ffffff;
    box-shadow: 0 4px 14px var(--primary-glow);
}
.btn-primary:hover {
    background-color: var(--primary-hover);
    transform: translateY(-1px);
}
.btn-secondary {
    background-color: transparent;
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}
.btn-secondary:hover {
    background-color: rgba(255, 255, 255, 0.05);
    border-color: var(--text-muted);
}
.btn-danger {
    background-color: var(--danger);
    color: #ffffff;
    box-shadow: 0 4px 14px var(--danger-glow);
}
.btn-danger:hover {
    background-color: var(--danger-hover);
    transform: translateY(-1px);
}
.btn-warning {
    background-color: var(--warning);
    color: #000000;
    box-shadow: 0 4px 14px var(--warning-glow);
}
.btn-warning:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}
.btn-small {
    padding: 6px 12px;
    font-size: 0.85rem;
}

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}
.badge-admin {
    background-color: rgba(99, 102, 241, 0.15);
    color: #a5b4fc;
    border: 1px solid rgba(99, 102, 241, 0.3);
}
.badge-user {
    background-color: rgba(148, 163, 184, 0.15);
    color: #cbd5e1;
    border: 1px solid rgba(148, 163, 184, 0.3);
}
.badge-active {
    background-color: rgba(16, 185, 129, 0.15);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.3);
}
.badge-maintenance {
    background-color: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
    text-align: left;
}
.form-label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 8px;
}
.form-control {
    width: 100%;
    font-family: var(--font);
    font-size: 0.95rem;
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 12px 16px;
    color: #ffffff;
    transition: var(--transition-fast);
}
.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-glow);
}
select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px;
    padding-right: 40px;
}
textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

/* Alerts */
.alert {
    padding: 14px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    font-size: 0.95rem;
    font-weight: 500;
    border: 1px solid transparent;
}
.alert-error {
    background-color: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
}
.alert-success {
    background-color: rgba(16, 185, 129, 0.1);
    border-color: rgba(16, 185, 129, 0.2);
    color: #a7f3d0;
}

/* ==========================================================================
   Sidebar Navigation
   ========================================================================== */
.sidebar {
    width: var(--sidebar-width);
    background-color: var(--bg-secondary);
    border-right: 1px solid var(--border-color);
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    display: flex;
    flex-direction: column;
    padding: 30px 20px;
    transition: var(--transition-smooth);
}
.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.3rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 40px;
    padding-left: 10px;
}
.sidebar-logo span {
    background: linear-gradient(to right, #6366f1, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.sidebar-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background-color: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    margin-bottom: 30px;
}
.sidebar-user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, var(--primary), var(--accent-classroom));
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #ffffff;
    font-size: 1.1rem;
}
.sidebar-user-info {
    min-width: 0;
}
.sidebar-user-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sidebar-user-role {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-secondary);
    text-decoration: none;
    padding: 12px 16px;
    border-radius: 8px;
    font-weight: 500;
    transition: var(--transition-fast);
}
.sidebar-link:hover, .sidebar-link.active {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.05);
}
.sidebar-link.active {
    color: #ffffff;
    background-color: rgba(99, 102, 241, 0.1);
    border-left: 3px solid var(--primary);
    padding-left: 13px;
}
.sidebar-footer {
    margin-top: auto;
}

/* Hamburger toggle for mobile layout */
.mobile-header {
    display: none;
    justify-content: space-between;
    align-items: center;
    padding: 15px 24px;
    background-color: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    position: sticky;
    top: 0;
    z-index: 101;
}
.mobile-title {
    font-weight: 700;
    font-size: 1.1rem;
    color: #ffffff;
}
.menu-toggle {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.menu-toggle span {
    display: block;
    width: 24px;
    height: 2px;
    background-color: var(--text-primary);
    transition: var(--transition-fast);
}

/* ==========================================================================
   Login Page Styling
   ========================================================================== */
.login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}
.login-card {
    max-width: 440px;
    width: 100%;
    text-align: center;
}
.login-header {
    margin-bottom: 30px;
}
.login-logo {
    font-size: 2.2rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 10px;
}
.login-logo span {
    background: linear-gradient(to right, #6366f1, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.login-subtitle {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

/* ==========================================================================
   Dashboard Calendar UI Elements
   ========================================================================== */
.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
}

/* Calendar Control Bar */
.calendar-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
}
.calendar-nav-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
}
.calendar-title-text {
    font-size: 1.4rem;
    font-weight: 700;
    color: #ffffff;
    min-width: 180px;
    text-align: center;
}
.calendar-filters {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.filter-select {
    min-width: 160px;
}

/* Calendar Grid */
.calendar-container {
    background-color: rgba(255, 255, 255, 0.01);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
}
.calendar-days-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background-color: var(--bg-tertiary);
    border-bottom: 1px solid var(--border-color);
    text-align: center;
}
.calendar-day-label {
    padding: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-auto-rows: minmax(110px, auto);
}
.calendar-cell {
    border-right: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    padding: 8px;
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: var(--transition-fast);
}
.calendar-cell:nth-child(7n) {
    border-right: none;
}
.calendar-cell.other-month {
    opacity: 0.25;
}
.calendar-cell.today {
    background-color: rgba(99, 102, 241, 0.03);
}
.calendar-cell.today .calendar-date-number {
    background: var(--primary);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
.calendar-date-number {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-secondary);
    margin-bottom: 6px;
    align-self: flex-start;
}
.calendar-cell-events {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
    overflow-y: auto;
}

/* Event Slots */
.calendar-event {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: var(--transition-fast);
    border: 1px solid transparent;
}
.calendar-event:hover {
    transform: translateX(1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}
.event-lab {
    background-color: rgba(16, 185, 129, 0.12);
    color: #6ee7b7;
    border-color: rgba(16, 185, 129, 0.3);
}
.event-classroom {
    background-color: rgba(139, 92, 246, 0.12);
    color: #c084fc;
    border-color: rgba(139, 92, 246, 0.3);
}

.calendar-cell-add-btn {
    position: absolute;
    right: 8px;
    top: 8px;
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.1rem;
    cursor: pointer;
    opacity: 0;
    transition: var(--transition-fast);
}
.calendar-cell:hover .calendar-cell-add-btn {
    opacity: 1;
}
.calendar-cell-add-btn:hover {
    color: var(--primary);
}

/* Legend styling */
.calendar-legend {
    display: flex;
    gap: 20px;
    margin-top: 15px;
    font-size: 0.85rem;
    color: var(--text-secondary);
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
.legend-color {
    width: 14px;
    height: 14px;
    border-radius: 4px;
}

/* ==========================================================================
   Modals
   ========================================================================== */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(5, 7, 12, 0.85);
    backdrop-filter: blur(8px);
    z-index: 1000;
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    padding: 20px;
}
.modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
}
.modal-window {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
    width: 100%;
    max-width: 500px;
    transform: scale(0.95);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}
.modal-overlay.active .modal-window {
    transform: scale(1);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background-color: var(--bg-tertiary);
}
.modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #ffffff;
}
.modal-close {
    background: none;
    border: none;
    color: var(--text-secondary);
    font-size: 1.5rem;
    cursor: pointer;
    line-height: 1;
}
.modal-close:hover {
    color: #ffffff;
}
.modal-body {
    padding: 24px;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    background-color: var(--bg-tertiary);
    border-top: 1px solid var(--border-color);
}

/* ==========================================================================
   Admin Tables
   ========================================================================== */
.table-responsive {
    overflow-x: auto;
    width: 100%;
}
.admin-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 0.95rem;
}
.admin-table th {
    background-color: var(--bg-tertiary);
    color: var(--text-secondary);
    font-weight: 600;
    padding: 16px;
    border-bottom: 1px solid var(--border-color);
}
.admin-table td {
    padding: 16px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-primary);
}
.admin-table tr:hover {
    background-color: rgba(255, 255, 255, 0.01);
}
.admin-table tr:last-child td {
    border-bottom: none;
}
.actions-column {
    display: flex;
    gap: 8px;
}

/* Info Summary Card inside Dashboard Sidebar etc */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    background-color: rgba(255, 255, 255, 0.01);
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #ffffff;
}
.stat-label {
    font-size: 0.85rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Tooltips & Event descriptions */
.tooltip {
    position: absolute;
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 10px 14px;
    color: var(--text-primary);
    font-size: 0.8rem;
    z-index: 10;
    pointer-events: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    max-width: 200px;
    display: none;
}

/* ==========================================================================
   Responsive Breakpoints
   ========================================================================== */
@media (max-width: 1024px) {
    .main-content {
        padding: 30px;
    }
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    .sidebar.active {
        transform: translateX(0);
    }
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    .mobile-header {
        display: flex;
    }
    .calendar-days-header {
        display: none; /* Hide day labels on compact screen to save space, or display initials */
    }
    .calendar-grid {
        grid-auto-rows: minmax(70px, auto);
    }
    .calendar-cell {
        padding: 4px;
        min-height: 70px;
    }
    .calendar-date-number {
        font-size: 0.8rem;
        margin-bottom: 2px;
    }
    .calendar-event {
        font-size: 0.65rem;
        padding: 2px 4px;
    }
    .calendar-controls {
        flex-direction: column;
        align-items: stretch;
    }
    .calendar-filters {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-select {
        width: 100%;
    }
}
