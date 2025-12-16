<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    </style>
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
                <a href="<?= base_url('dashboard') ?>" class="nav-link <?= (current_url() == base_url('dashboard') || strpos(current_url(), 'dashboard') !== false && strpos(current_url(), 'dashboard/') === false) ? 'active' : '' ?>">
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

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Status Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card status-banner">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-<?= $status['color'] == 'danger' ? 'exclamation-triangle' : 
                                                 ($status['color'] == 'warning' ? 'exclamation-circle' : 'check-circle') ?> 
                                                 me-3 fs-4" style="color: <?= $status['color'] == 'danger' ? '#ff6b6b' : ($status['color'] == 'warning' ? '#ffc107' : '#4eac9b') ?>"></i>
                                <div>
                                    <h5 class="card-title mb-1" style="color: #4eac9b;">System Status: <?= ucfirst($status['status']) ?></h5>
                                    <p class="card-text mb-0" style="color: #b0c4be;"><?= $status['message'] ?></p>
                                </div>
                            </div>
                            <div>
                                <span class="badge" style="background: <?= $status['color'] == 'danger' ? '#ff6b6b' : ($status['color'] == 'warning' ? '#ffc107' : '#4eac9b') ?>; color: white; padding: 8px 16px;">
                                    <?= strtoupper($status['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Readings with Gauges -->
        <div class="row mb-4">
            <!-- Temperature Gauge -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-thermometer-half me-2" style="color: #ffd700;"></i>
                        <span style="color: white; font-weight: 600;">Temperature</span>
                    </div>
                    <div class="card-body">
                        <?php if ($currentReading): ?>
                            <div class="gauge-container position-relative" style="height: 180px;">
                                <canvas id="tempGauge" height="180"></canvas>
                                <div class="gauge-value position-absolute top-50 start-50 translate-middle text-center">
                                    <div class="gauge-primary-value display-5 fw-bold" style="color: #4eac9b;">
                                        <?= number_format($currentReading['temperature'], 1) ?>
                                    </div>
                                    <div class="gauge-unit" style="color: #b0c4be;">°C</div>
                                </div>
                                <div class="gauge-threshold text-center mt-3">
                                    <small style="color: #b0c4be;">
                                        Range: <?= $status['thresholds']['temp_min'] ?>-<?= $status['thresholds']['temp_max'] ?>°C
                                    </small>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <?php if ($currentReading['temperature'] < $status['thresholds']['temp_min']): ?>
                                    <span class="badge" style="background: #ffc107; color: #1a3a3a;">Low</span>
                                <?php elseif ($currentReading['temperature'] > $status['thresholds']['temp_max']): ?>
                                    <span class="badge" style="background: #ff6b6b; color: white;">High</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #4eac9b; color: white;">Normal</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-thermometer-empty fa-3x mb-3" style="color: #4eac9b; opacity: 0.5;"></i>
                                <p class="mb-0" style="color: #b0c4be;">No temperature data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- pH Level Gauge -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-vial me-2" style="color: #ffd700;"></i>
                        <span style="color: white; font-weight: 600;">pH Level</span>
                    </div>
                    <div class="card-body">
                        <?php if ($currentReading): ?>
                            <div class="gauge-container position-relative" style="height: 180px;">
                                <canvas id="phGauge" height="180"></canvas>
                                <div class="gauge-value position-absolute top-50 start-50 translate-middle text-center">
                                    <div class="gauge-primary-value display-5 fw-bold" style="color: #4eac9b;">
                                        <?= number_format($currentReading['ph_level'], 2) ?>
                                    </div>
                                </div>
                                <div class="gauge-threshold text-center mt-3">
                                    <small style="color: #b0c4be;">
                                        Range: <?= number_format($status['thresholds']['ph_min'], 1) ?>-<?= number_format($status['thresholds']['ph_max'], 1) ?>
                                    </small>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <?php if ($currentReading['ph_level'] < $status['thresholds']['ph_min']): ?>
                                    <span class="badge" style="background: #ff6b6b; color: white;">Acidic</span>
                                <?php elseif ($currentReading['ph_level'] > $status['thresholds']['ph_max']): ?>
                                    <span class="badge" style="background: #ff6b6b; color: white;">Alkaline</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #4eac9b; color: white;">Normal</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-vial fa-3x mb-3" style="color: #4eac9b; opacity: 0.5;"></i>
                                <p class="mb-0" style="color: #b0c4be;">No pH data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Turbidity Gauge -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-water me-2" style="color: #ffd700;"></i>
                        <span style="color: white; font-weight: 600;">Turbidity</span>
                    </div>
                    <div class="card-body">
                        <?php if ($currentReading): ?>
                            <div class="gauge-container position-relative" style="height: 180px;">
                                <canvas id="turbidityGauge" height="180"></canvas>
                                <div class="gauge-value position-absolute top-50 start-50 translate-middle text-center">
                                    <div class="gauge-primary-value display-5 fw-bold" style="color: #4eac9b;">
                                        <?= number_format($currentReading['turbidity'], 0) ?>
                                    </div>
                                    <div class="gauge-unit" style="color: #b0c4be;">NTU</div>
                                </div>
                                <div class="gauge-threshold text-center mt-3">
                                    <small style="color: #b0c4be;">
                                        Max: <?= $status['thresholds']['turbidity_max'] ?> NTU
                                    </small>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <?php if ($currentReading['turbidity'] > $status['thresholds']['turbidity_max']): ?>
                                    <span class="badge" style="background: #ff6b6b; color: white;">High</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #4eac9b; color: white;">Normal</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-water fa-3x mb-3" style="color: #4eac9b; opacity: 0.5;"></i>
                                <p class="mb-0" style="color: #b0c4be;">No turbidity data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-chart-line me-2" style="color: #ffd700;"></i>
                        <span style="color: white; font-weight: 600;">Sensor Trends (Last 24 Hours)</span>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px;">
                            <canvas id="sensorChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts and Devices -->
        <div class="row">
            <!-- Recent Alerts -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bell me-2" style="color: #ffd700;"></i>
                            <span style="color: white; font-weight: 600;">Recent Alerts</span>
                            <?php if ($unreadAlerts > 0): ?>
                                <span class="badge ms-2" style="background: #ff6b6b; color: white;"><?= $unreadAlerts ?> new</span>
                            <?php endif; ?>
                        </div>
                        <a href="<?= base_url('dashboard/alerts') ?>" style="color: white; text-decoration: none; font-weight: 600;">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($alerts)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x mb-3" style="color: #4eac9b; opacity: 0.5;"></i>
                                <p class="mb-0" style="color: #b0c4be;">No recent alerts</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($alerts as $alert): ?>
                                    <div class="mb-3 p-3 rounded" style="background: rgba(78, 172, 155, 0.1); border-left: 4px solid <?= $alert['level'] == 'danger' ? '#ff6b6b' : ($alert['level'] == 'warning' ? '#ffc107' : '#4eac9b') ?>;">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0 fw-semibold" style="color: white;"><?= ucfirst($alert['type']) ?></h6>
                                            <small style="color: #b0c4be;"><?= date('H:i', strtotime($alert['created_at'])) ?></small>
                                        </div>
                                        <p class="mb-2" style="color: #b0c4be;"><?= htmlspecialchars($alert['message']) ?></p>
                                        <span class="badge" style="background: <?= $alert['level'] == 'danger' ? '#ff6b6b' : ($alert['level'] == 'warning' ? '#ffc107' : '#4eac9b') ?>; color: white;">
                                            <?= strtoupper($alert['level']) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Device Control -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-cogs me-2" style="color: #4eac9b;"></i>
                                <h5 class="mb-0">Device Control</h5>
                            </div>
                            <a href="<?= base_url('dashboard/devices') ?>" class="btn btn-sm btn-outline-light">
                                <i class="fas fa-external-link-alt me-1"></i> Manage
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Oxygenator -->
                                <div class="card-body p-4">
                                    <div class="row g-4">

                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center p-4 border border-secondary rounded" style="background: var(--accent-soft);">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-wind me-5" style="font-size: 2.5rem; color: #66CFC4;"></i>
                                                    <div>
                                                        <h4 class="mb-2 fw-bold" style="color: #66CFC4;">Oxygenator</h4>

                                                        <span class="text-white fw-normal">
                                                            <i style="color: #66CFC4;" class="far fa-clock me-1"></i>
                                                            Last Update:
                                                            <span class="fw-medium" style="color: var(--accent);">
                                                                <?= $deviceStatus['oxygenator']['last_updated'] ?
                                                                    date('M j, g:i A', strtotime($deviceStatus['oxygenator']['last_updated'])) :
                                                                    'Never' ?>
                                                            </span>
                                                        </span>
                                                        <br>
                                                        <span class="text-white fw-normal">
                                                            <i class="fas fa-user me-1"></i>
                                                            Triggered By:
                                                            <span class="fw-medium" style="color: var(--accent);">
                                                                <?= ucfirst($deviceStatus['oxygenator']['triggered_by']) ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="badge bg-<?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'success' : 'secondary' ?> px-4 py-3 fs-5 fw-bold">
                                                    <?= $deviceStatus['oxygenator']['state'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    <!-- Water Pump -->
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center p-4 border border-secondary rounded" style="background: var(--accent-soft);">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-tint me-5" style="font-size: 2.5rem; color: #66CFC4;"></i>
                                                    <div>
                                                        <h4 class="mb-2 fw-bold" style="color: #66CFC4;">Water Pump</h4>

                                                        <span class="text-white fw-normal">
                                                            <i style="color: #66CFC4;" class="far fa-clock me-1"></i>
                                                            Last Update:
                                                            <span class="fw-medium" style="color: var(--accent);">
                                                                <?= $deviceStatus['water_pump']['last_updated'] ?
                                                                    date('M j, g:i A', strtotime($deviceStatus['water_pump']['last_updated'])) :
                                                                    'Never' ?>
                                                            </span>
                                                        </span>
                                                        <br>
                                                        <span class="text-white fw-normal">
                                                            <i class="fas fa-user me-1"></i>
                                                            Triggered By:
                                                            <span class="fw-medium" style="color: var(--accent);">
                                                                <?= ucfirst($deviceStatus['water_pump']['triggered_by']) ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="badge bg-<?= $deviceStatus['water_pump']['state'] == 'ON' ? 'success' : 'secondary' ?> px-4 py-3 fs-5 fw-bold">
                                                    <?= $deviceStatus['water_pump']['state'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
                        
    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Sidebar Toggle Functionality
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleBtn');

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

        // Active navigation item highlighting
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
            link.addEventListener('click', function() {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        /* === GAUGE CHARTS CONFIGURATION === */
        function createGauge(chartId, value, min, max, unit = '') {
            const ctx = document.getElementById(chartId).getContext('2d');
            
            // Determine color based on value
            let color = '#4eac9b'; // Verdigris (success)
            if (value < min * 0.9 || value > max * 1.1) {
                color = '#ff6b6b'; // Red (danger)
            } else if (value < min || value > max) {
                color = '#ffc107'; // Yellow (warning)
            }
            
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [value, max - value],
                        backgroundColor: [color, '#666'], // Value color, background color
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                        borderRadius: 5,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: false
                    }
                }
            });
        }

        /* === INITIALIZE GAUGES === */
        let tempGauge, phGauge, turbidityGauge;

        document.addEventListener('DOMContentLoaded', function() {
            tempGauge = createGauge('tempGauge', 24.5, 20, 28, '°C');
            phGauge = createGauge('phGauge', 6.8, 6.5, 7.5);
            turbidityGauge = createGauge('turbidityGauge', 2.5, 0, 5, 'NTU');
            
            // Initialize chart
            initSensorChart();
        });

        /* === SENSOR CHART === */
        function initSensorChart() {
            const ctx = document.getElementById('sensorChart').getContext('2d');
            window.sensorChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
                    datasets: [
                        {
                            label: 'Temperature (°C)',
                            data: [22, 22.5, 23, 24, 24.5, 25, 26, 26.5, 25, 24.5, 23.5, 23],
                            borderColor: '#4eac9b',
                            backgroundColor: 'rgba(78, 172, 155, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'pH Level',
                            data: [6.8, 6.85, 6.9, 6.95, 7, 7.05, 7.1, 7.05, 7, 6.95, 6.9, 6.85],
                            borderColor: '#2d8f7f',
                            backgroundColor: 'rgba(45, 143, 127, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Turbidity (NTU)',
                            data: [2, 2.2, 2.3, 2.5, 2.4, 2.6, 2.8, 2.7, 2.5, 2.3, 2.1, 2],
                            borderColor: '#ffd700',
                            backgroundColor: 'rgba(255, 215, 0, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#ffffff',
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(15, 38, 38, 0.9)',
                            titleColor: '#FFFFFF',
                            bodyColor: '#b0c4be',
                            borderColor: '#4eac9b',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(78, 172, 155, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#ffffff',
                                maxTicksLimit: 10
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: 'rgba(78, 172, 155, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#ffffff'
                            },
                            title: {
                                display: true,
                                text: 'Temperature / Turbidity',
                                color: '#ffffff'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                color: '#ffffff'
                            },
                            title: {
                                display: true,
                                text: 'pH Level',
                                color: '#ffffff'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }
    </script>
</body>
</html>