<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AquaSense Dashboard' ?></title>
    <!-- CSRF token for AJAX requests -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2d5a5a 0%, #1a3a3a 50%, #0f2626 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, rgba(78, 172, 155, 0.1) 0%, rgba(45, 90, 90, 0.2) 100%);
            border-right: 2px solid #4eac9b;
            z-index: 1000;
            transition: all 0.4s ease;
            overflow-y: auto;
            padding: 20px;
        }

        .sidebar.collapsed {
            width: 100px;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(78, 172, 155, 0.3);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-logo {
            justify-content: center;
            width: 100%;
        }

        .logo-svg {
            width: 45px;
            height: 45px;
        }

        .logo-text {
            color: #4eac9b;
            font-weight: 700;
            font-size: 1.3rem;
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        .toggle-btn {
            background: linear-gradient(135deg, #4eac9b 0%, #2d8f7f 100%);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .toggle-btn:hover {
            box-shadow: 0 5px 15px rgba(78, 172, 155, 0.3);
            transform: scale(1.05);
        }

        .sidebar.collapsed .toggle-btn {
            width: 100%;
        }

        /* Navigation Menu */
        .nav-menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            color: #b0c4be;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: rgba(78, 172, 155, 0.2);
            color: #4eac9b;
            padding-left: 20px;
        }

        .nav-link.active {
            background: linear-gradient(135deg, rgba(78, 172, 155, 0.3) 0%, rgba(45, 90, 90, 0.3) 100%);
            color: #4eac9b;
            border-left: 4px solid #4eac9b;
            padding-left: 11px;
        }

        .nav-icon {
            min-width: 24px;
            font-size: 1.2rem;
            color: #ffd700;
        }

        .nav-text {
            flex: 1;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        /* Section Title */
        .nav-section-title {
            color: #4eac9b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 25px;
            margin-bottom: 12px;
            padding: 0 15px;
            font-weight: 700;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-section-title {
            opacity: 0;
            height: 0;
            margin: 0;
            padding: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            transition: all 0.4s ease;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 100px;
        }

        /* Scrollbar Style */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(78, 172, 155, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #4eac9b;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #2d8f7f;
        }

        /* Status Banner */
        .status-banner {
            background: linear-gradient(135deg, rgba(78, 172, 155, 0.15) 0%, rgba(45, 90, 90, 0.2) 100%) !important;
            border: 2px solid #4eac9b !important;
            border-radius: 15px;
        }

        .card {
            background: linear-gradient(135deg, rgba(78, 172, 155, 0.1) 0%, rgba(45, 90, 90, 0.15) 100%) !important;
            border: 2px solid #4eac9b !important;
            border-radius: 15px;
        }

        .card-header {
            background: linear-gradient(135deg, #4eac9b 0%, #2d8f7f 100%) !important;
            border: none !important;
        }

        .card-body {
            background: rgba(15, 38, 38, 0.5) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100px;
            }

            .main-content {
                margin-left: 100px;
            }

            .logo-text,
            .nav-text,
            .nav-section-title {
                opacity: 0;
                display: none;
            }
        }

        /* Table Styles */
        .table {
            color: white;
        }

        .table thead th {
            color: #4eac9b;
            border-bottom: 2px solid rgba(78, 172, 155, 0.3);
            font-weight: 600;
        }

        .table tbody tr {
            border-bottom: 1px solid rgba(78, 172, 155, 0.2);
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            color: #4eac9b;
            background: rgba(173, 224, 216, 0.1) !important;
        }

        .table tbody td {
            color: #b0c4be;
            vertical-align: middle;
        }

        /* Badge Styles */
        .badge {
            padding: 6px 12px;
            font-weight: 600;
            border-radius: 8px;
            color: white !important;
        }

        .bg-accent {
            background: #4eac9b !important;
        }

        /* Button Styles */
        .btn-outline-secondary {
            color: #4eac9b !important;
            border-color: #4eac9b !important;
        }

        .btn-outline-secondary:hover {
            background: #4eac9b !important;
            color: white !important;
        }

        .btn-outline-primary {
            color: #4eac9b !important;
            border-color: #4eac9b !important;
        }

        .btn-outline-primary:hover {
            background: #4eac9b !important;
            color: white !important;
        }

        /* DataTables conditional loading */
        <?php if (isset($load_datatables) && $load_datatables): ?>
        .dataTables_wrapper {
            color: white !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info {
            color: #b0c4be !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            background-color: rgba(78, 172, 155, 0.1) !important;
            border: 1px solid #4eac9b !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_filter input::placeholder {
            color: #b0c4be !important;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #4eac9b !important;
            box-shadow: 0 0 0 0.2rem rgba(78, 172, 155, 0.25) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: rgba(78, 172, 155, 0.1) !important;
            border: 1px solid #4eac9b !important;
            color: #4eac9b !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #4eac9b !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4eac9b !important;
            border-color: #4eac9b !important;
            color: white !important;
        }
        <?php endif; ?>

        /* for alerts */
        /* Alerts-specific styles - Add these to the existing style section in header.php */

/* Animated background elements */
.bg-elements {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
    z-index: 1;
}

.water-bubble {
    position: absolute;
    background: radial-gradient(circle at 30% 30%, rgba(78, 172, 155, 0.15), transparent);
    border-radius: 50%;
    opacity: 0.5;
    animation: float 6s infinite ease-in-out;
}

@keyframes float {
    0%, 100% { transform: translateY(0) translateX(0); }
    50% { transform: translateY(-20px) translateX(10px); }
}

@keyframes glow {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.8; }
}

.glow-line {
    position: absolute;
    height: 1px;
    background: linear-gradient(90deg, transparent, #4eac9b, transparent);
    animation: glow 3s infinite;
}

@keyframes rise {
    0% {
        opacity: 0;
        transform: translateY(100vh) translateX(0);
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: translateY(-100vh) translateX(100px);
    }
}

.bubble {
    position: absolute;
    bottom: 0;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, rgba(159, 219, 205, 0.4), rgba(78, 172, 155, 0.1));
    border: 1px solid rgba(78, 172, 155, 0.2);
    animation: rise linear infinite;
}

.bubble::before {
    content: '';
    position: absolute;
    top: 10%;
    left: 15%;
    width: 30%;
    height: 30%;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.8), transparent);
    border-radius: 50%;
}

/* List Group Styles */
.list-group {
    border: none;
}

.list-group-item {
    background: rgba(78, 172, 155, 0.1) !important;
    border: 1px solid rgba(78, 172, 155, 0.2) !important;
    border-radius: 10px !important;
    color: white;
}

.alert-item {
    transition: all 0.3s ease;
    padding: 15px !important;
    margin-bottom: 12px !important;
}

.alert-item:hover {
    background: rgba(78, 172, 155, 0.15) !important;
    transform: translateX(5px);
}

.alert-item.alert-danger {
    background: rgba(255, 107, 107, 0.15) !important;
    border-left: 4px solid #ff6b6b !important;
}

.alert-item.alert-warning {
    background: rgba(255, 199, 0, 0.15) !important;
    border-left: 4px solid #ffc107 !important;
}

.alert-item.alert-info {
    background: rgba(78, 172, 155, 0.15) !important;
    border-left: 4px solid #4eac9b !important;
}

/* Badge Styles */
.badge {
    padding: 6px 12px;
    font-weight: 600;
    border-radius: 8px;
}

.bg-danger {
    background: #ff6b6b !important;
}

.bg-warning {
    background: #ffc107 !important;
}

.bg-info {
    background: #4eac9b !important;
}

.bg-secondary {
    background: #666 !important;
}

/* Button Styles */
.btn {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-light {
    background: rgba(255, 255, 255, 0.9) !important;
    color: #1a3a3a !important;
}

.btn-light:hover {
    background: white !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.btn-outline-danger {
    color: #ff6b6b !important;
    border-color: #ff6b6b !important;
}

.btn-outline-danger:hover {
    background: #ff6b6b !important;
    color: white !important;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.85rem;
}

/* Text Styles */
h6 {
    color: white;
    font-weight: 600;
}

p {
    color: #b0c4be;
}

.text-muted {
    color: #b0c4be !important;
}

.text-success {
    color: #4eac9b !important;
}

.text-center {
    text-align: center;
}

/* Empty State */
.fa-check-circle {
    color: #4eac9b;
}

/* Pagination */
.pagination {
    margin-top: 30px;
}

.page-link {
    background: rgba(78, 172, 155, 0.1);
    color: #4eac9b;
    border: 1px solid #4eac9b;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #4eac9b;
    color: white;
}

.page-link.active {
    background: #4eac9b;
    border-color: #4eac9b;
    color: white;
}
/* Old alerts styling */
.old-alert {
    opacity: 0.7;
}

.old-alert:hover {
    opacity: 1;
}

.old-alert .badge::after {
    content: ' ⏳';
    font-size: 0.8em;
}

/* Device Cards */
.device-card {
    background: linear-gradient(135deg, rgba(78, 172, 155, 0.15) 0%, rgba(45, 90, 90, 0.2) 100%) !important;
    border: 2px solid #666 !important;
    border-radius: 15px;
    transition: all 0.3s ease;
}

.device-card.device-on {
    border-color: #4eac9b !important;
    box-shadow: 0 0 20px rgba(78, 172, 155, 0.3);
}

.device-card.device-off {
    border-color: #666 !important;
    opacity: 0.8;
}

.device-icon {
    font-size: 3rem;
    color: #666;
    transition: all 0.3s ease;
    margin-bottom: 15px;
}

.device-card.device-on .device-icon {
    color: #4eac9b;
    filter: drop-shadow(0 0 10px rgba(78, 172, 155, 0.5));
}

.device-card .card-title {
    color: white;
    font-weight: 600;
}

.device-card .form-check-label {
    color: white;
    cursor: pointer;
}

.device-card small {
    color: #b0c4be;
}

/* Status badge */
.status-badge {
    background: linear-gradient(135deg, #4eac9b 0%, #2d8f7f 100%) !important;
    color: white !important;
}

/* Alert Styles */
.alert {
    border-radius: 12px;
    border: 1px solid;
}

.alert-success {
    background: rgba(78, 172, 155, 0.15) !important;
    border-color: rgba(78, 172, 155, 0.5) !important;
    color: #4eac9b !important;
}

.alert-danger {
    background: rgba(255, 107, 107, 0.15) !important;
    border-color: rgba(255, 107, 107, 0.5) !important;
    color: #ff9999 !important;
}

.btn-close {
    filter: invert(1) brightness(1.2);
}

/* Form Styles */
.form-label {
    color: white;
    font-weight: 600;
    margin-bottom: 8px;
}

.form-control {
    background: rgba(78, 172, 155, 0.1) !important;
    border: 2px solid #4eac9b !important;
    color: white !important;
    border-radius: 10px;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.form-control::placeholder {
    color: rgba(176, 196, 190, 0.5) !important;
}

.form-control:focus {
    background: rgba(78, 172, 155, 0.15) !important;
    border-color: #4eac9b !important;
    box-shadow: 0 0 0 0.3rem rgba(78, 172, 155, 0.25) !important;
    color: white !important;
}

/* Form Select Styles */
.form-select {
    background: rgba(78, 172, 155, 0.1) !important;
    border: 2px solid #4eac9b !important;
    color: white !important;
    border-radius: 10px;
    padding: 8px 35px 8px 15px;
    transition: all 0.3s ease;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234eac9b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    background-size: 16px 12px !important;
}

.form-select:focus {
    background: rgba(78, 172, 155, 0.15) !important;
    border-color: #4eac9b !important;
    box-shadow: 0 0 0 0.3rem rgba(78, 172, 155, 0.25) !important;
    color: white !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234eac9b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    background-size: 16px 12px !important;
}

.form-select option {
    background: #1a3a3a !important;
    color: white !important;
}

.form-select-sm {
    padding: 6px 30px 6px 12px;
    font-size: 0.875rem;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234eac9b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 10px center !important;
    background-size: 14px 10px !important;
}

/* Form Text */
.form-text {
    color: #b0c4be !important;
    font-size: 0.85rem;
    margin-top: 5px;
}

/* Button Groups */
.btn-group {
    gap: 10px;
    display: flex;
    flex-wrap: wrap;
}

.btn-check:checked + .btn-outline-accent,
.btn-outline-accent:hover {
    background: #4eac9b !important;
    border-color: #4eac9b !important;
    color: white !important;
}

.btn-outline-accent {
    color: #4eac9b !important;
    border: 2px solid #4eac9b !important;
    border-radius: 10px;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 120px;
}

/* Button Styles */
.btn-accent {
    background: linear-gradient(135deg, #4eac9b 0%, #2d8f7f 100%);
    color: white !important;
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-accent:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(78, 172, 155, 0.3);
}

/* Checkbox Styles (not switches) */
.form-check:not(.form-switch) .form-check-input {
    width: 20px;
    height: 20px;
    background: rgba(78, 172, 155, 0.1) !important;
    border: 2px solid #4eac9b !important;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-check:not(.form-switch) .form-check-input:hover {
    background: rgba(78, 172, 155, 0.2) !important;
}

.form-check:not(.form-switch) .form-check-input:checked {
    background: #4eac9b !important;
    border-color: #4eac9b !important;
}

/* Form Switch (Toggle) Styles */
.form-switch .form-check-input {
    width: 3em !important;
    height: 1.5em !important;
    background-color: rgba(102, 102, 102, 0.5) !important;
    border: 2px solid #666 !important;
    border-radius: 1.5em !important;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: none !important;
}

.form-switch .form-check-input:checked {
    background-color: #4eac9b !important;
    border-color: #4eac9b !important;
    background-image: none !important;
}

.form-switch .form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(78, 172, 155, 0.25) !important;
    border-color: #4eac9b !important;
}

.form-switch .form-check-input:hover {
    border-color: #4eac9b !important;
}

/* Switch toggle circle */
.form-switch .form-check-input::before {
    content: '';
    position: absolute;
    width: 1.2em;
    height: 1.2em;
    border-radius: 50%;
    background-color: white;
    top: 50%;
    left: 0.15em;
    transform: translateY(-50%);
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.form-switch .form-check-input:checked::before {
    left: calc(100% - 1.35em);
    background-color: white;
}

.form-switch .form-check-input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-check-label {
    color: white;
    cursor: pointer;
    margin-left: 10px;
}

/* List Styles */
.list-unstyled li {
    color: #b0c4be;
    line-height: 1.8;
}

.list-unstyled i {
    color: #4eac9b;
}

/* Alert Styles */
.alert {
    border-radius: 12px;
    border: 1px solid;
}

.alert-success {
    background: rgba(78, 172, 155, 0.15) !important;
    border-color: #4eac9b !important;
    color: #4eac9b !important;
}

.alert-danger {
    background: rgba(255, 107, 107, 0.15) !important;
    border-color: #ff6b6b !important;
    color: #ff9999 !important;
}

.btn-close {
    filter: invert(1) brightness(1.2);
}

    </style>

    <!-- DataTables CSS - Load only if needed -->
    <?php if (isset($load_datatables) && $load_datatables): ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <svg class="logo-svg" viewBox="0 0 540 220" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#4eac9b;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#2d8f7f;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <circle cx="110" cy="110" r="100" fill="none" stroke="url(#logoGrad)" stroke-width="12" opacity="0.9"/>
                    <path d="M 50 130 Q 70 120 90 130 T 130 130" fill="none" stroke="url(#logoGrad)" stroke-width="3" opacity="0.7"/>
                    <path d="M 40 145 Q 60 135 80 145 T 120 145 T 160 145" fill="none" stroke="url(#logoGrad)" stroke-width="2" opacity="0.5"/>
                    <polyline points="70,110 85,100 95,125 105,95 115,115 130,110 145,100 155,125" 
                              fill="none" stroke="#ffd700" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" opacity="0.95"/>
                </svg>
                <span class="logo-text">AquaSense</span>
            </div>
            <button class="toggle-btn" id="toggleBtn" title="Toggle Sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>

         <!-- Navigation Menu -->
        <ul class="nav-menu">
            <li class="nav-section-title">Main</li>
            
            <li class="nav-item">
                <a href="<?= base_url('dashboard/main') ?>" class="nav-link <?= (current_url() == base_url('dashboard') || strpos(current_url(), 'dashboard') !== false && strpos(current_url(), 'dashboard/') === false) ? 'active' : '' ?>">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('dashboard/sensor-data') ?>" class="nav-link <?= (strpos(current_url(), 'sensor-data') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span class="nav-text">Sensor Data</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('dashboard/alerts') ?>" class="nav-link <?= (strpos(current_url(), 'alerts') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-bell nav-icon"></i>
                    <span class="nav-text">Alerts</span>
                    <?php if (isset($unreadAlerts) && $unreadAlerts > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?= $unreadAlerts ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('dashboard/devices') ?>" class="nav-link <?= (strpos(current_url(), 'devices') !== false || strpos(current_url(), 'devices') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-cogs nav-icon"></i>
                    <span class="nav-text">Device Control</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('dashboard/settings') ?>" class="nav-link <?= (strpos(current_url(), 'settings') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-sliders-h nav-icon"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>

            <li class="nav-section-title">Account</li>

            <li class="nav-item">
                <a href="<?= base_url('logout') ?>" class="nav-link logout-link">
                    <i class="fas fa-sign-out-alt nav-icon"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Container -->
    <div class="main-content" id="mainContent">
        <!-- Page content will be inserted here -->
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Basic sidebar toggle script -->
    <script>
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleBtn');
            
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    // Change arrow direction
                    const icon = toggleBtn.querySelector('i');
                    if (sidebar.classList.contains('collapsed')) {
                        icon.classList.remove('fa-chevron-left');
                        icon.classList.add('fa-chevron-right');
                    } else {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-left');
                    }
                });
            }
        });
    </script>

    <?= $this->include('layout/footer') ?>