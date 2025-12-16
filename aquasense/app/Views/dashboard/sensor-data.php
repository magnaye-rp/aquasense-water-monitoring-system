<?= $this->include('layout/header') ?>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    
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

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, rgba(78, 172, 155, 0.1) 0%, rgba(45, 90, 90, 0.2) 100%);
            border-right: 2px solid #4eac9b;
            z-index: 1050;
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
            position: relative;
            z-index: 10;
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

        /* Card Styles */
        .card {
            background: linear-gradient(135deg, rgba(78, 172, 155, 0.1) 0%, rgba(45, 90, 90, 0.15) 100%) !important;
            border: 2px solid #4eac9b !important;
            border-radius: 15px;
        }

        .card-header {
            background: linear-gradient(135deg, #4eac9b 0%, #2d8f7f 100%) !important;
            border: none !important;
            color: white !important;
        }

        .card-body {
            background: rgba(15, 38, 38, 0.5) !important;
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

        /* DataTable Styles */
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

        /* Filter Controls */
        .input-group-text {
            background-color: rgba(78, 172, 155, 0.1) !important;
            border: 1px solid #4eac9b !important;
            color: #ffd700 !important;
        }

        .form-select {
            background-color: rgba(78, 172, 155, 0.1) !important;
            border: 1px solid #4eac9b !important;
            color: white !important;
        }

        .form-select:focus {
            border-color: #4eac9b !important;
            box-shadow: 0 0 0 0.25rem rgba(78, 172, 155, 0.25) !important;
        }

        .form-select option {
            background: #1a3a3a;
            color: white;
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

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
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

        /* Modal Styles */
        .modal-content {
            background: linear-gradient(135deg, rgba(78, 172, 155, 0.1) 0%, rgba(45, 90, 90, 0.2) 100%) !important;
            border: 2px solid #4eac9b !important;
            color: white !important;
        }

        .modal-header {
            border-bottom: 2px solid rgba(78, 172, 155, 0.3) !important;
        }

        .modal-title {
            color: #4eac9b !important;
        }

        .btn-close {
            filter: invert(1) brightness(1.2);
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

        .alert-warning {
            background: rgba(255, 199, 0, 0.15) !important;
            border-color: #ffc107 !important;
            color: #ffc107 !important;
        }

        .alert-danger {
            background: rgba(255, 107, 107, 0.15) !important;
            border-color: #ff6b6b !important;
            color: #ff9999 !important;
        }

        .alert-secondary {
            background: rgba(78, 172, 155, 0.15) !important;
            border-color: #4eac9b !important;
            color: #b0c4be !important;
        }

        /* Info Box */
        .bg-light {
            background: rgba(78, 172, 155, 0.15) !important;
            border: 1px solid #4eac9b !important;
            color: #b0c4be !important;
        }

        .bg-light small {
            color: #b0c4be !important;
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

    <!-- Animated background -->
    <div class="bg-elements">
        <div class="water-bubble" style="width: 300px; height: 300px; top: 10%; left: 5%; animation-delay: 0s;"></div>
        <div class="water-bubble" style="width: 250px; height: 250px; top: 60%; right: 10%; animation-delay: 2s;"></div>
        <div class="glow-line" style="top: 30%; left: 0; width: 100%; animation-delay: 0s;"></div>
        <div class="glow-line" style="top: 70%; left: 0; width: 100%; animation-delay: 1s;"></div>
        
        <!-- Floating bubbles -->
        <div class="bubble" style="width: 40px; height: 40px; left: 10%; animation-duration: 8s; animation-delay: 0s;"></div>
        <div class="bubble" style="width: 60px; height: 60px; left: 20%; animation-duration: 10s; animation-delay: 1s;"></div>
        <div class="bubble" style="width: 30px; height: 30px; left: 30%; animation-duration: 12s; animation-delay: 2s;"></div>
        <div class="bubble" style="width: 50px; height: 50px; left: 15%; animation-duration: 9s; animation-delay: 3s;"></div>
        <div class="bubble" style="width: 35px; height: 35px; left: 25%; animation-duration: 11s; animation-delay: 4s;"></div>
        <div class="bubble" style="width: 45px; height: 45px; left: 35%; animation-duration: 10s; animation-delay: 5s;"></div>
    </div>

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
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-water me-2" style="color: #ffd700;"></i>
                    <span style="color: white; font-weight: 600;">Sensor Readings</span>
                </div>
                <div class="badge bg-accent">
                    <i class="fas fa-clock me-1"></i>
                    Real-time Data
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Controls -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-filter"></i></span>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="normal">Normal</option>
                                <option value="warning">Warning</option>
                                <option value="critical">Critical</option>
                                <option value="no_data">No Data</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-thermometer-half"></i></span>
                            <select class="form-select" id="tempFilter">
                                <option value="">All Temperatures</option>
                                <option value="low">&lt; <?= $thresholds['temp_min'] ?? 20 ?>°C</option>
                                <option value="normal"><?= $thresholds['temp_min'] ?? 20 ?>-<?= $thresholds['temp_max'] ?? 30 ?>°C</option>
                                <option value="high">&gt; <?= $thresholds['temp_max'] ?? 30 ?>°C</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tint"></i></span>
                            <select class="form-select" id="phFilter">
                                <option value="">All pH Levels</option>
                                <option value="low">&lt; <?= $thresholds['ph_min'] ?? 6.5 ?></option>
                                <option value="normal"><?= $thresholds['ph_min'] ?? 6.5 ?>-<?= $thresholds['ph_max'] ?? 8.5 ?></option>
                                <option value="high">&gt; <?= $thresholds['ph_max'] ?? 8.5 ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100" id="resetFilters">
                            <i class="fas fa-redo me-1"></i> Reset Filters
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="sensorReadingsTable">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Temperature (°C)</th>
                                <th>pH Level</th>
                                <th>Turbidity (NTU)</th>
                                <th>Status</th>
                                <th data-orderable="false">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($readings as $reading): ?>
                                <tr data-status="<?= strtolower($reading['status'] ?? 'normal') ?>"
                                    data-temperature="<?= $reading['temperature'] ?>"
                                    data-ph="<?= $reading['ph_level'] ?>"
                                    data-turbidity="<?= $reading['turbidity'] ?>"
                                    data-created-at="<?= $reading['created_at'] ?>"
                                    data-id="<?= $reading['id'] ?? '' ?>">
                                    <td data-order="<?= strtotime($reading['created_at']) ?>">
                                        <?= date('M d, Y H:i:s', strtotime($reading['created_at'])) ?>
                                    </td>
                                    <td>
                                        <span class="temp-value"><?= number_format($reading['temperature'], 1) ?></span>
                                    </td>
                                    <td>
                                        <span class="ph-value"><?= number_format($reading['ph_level'], 2) ?></span>
                                    </td>
                                    <td><?= number_format($reading['turbidity'], 0) ?></td>
                                    <td>
                                        <?php 
                                            $status = strtolower($reading['status'] ?? 'normal');
                                            $badgeClass = 'bg-accent';
                                            $statusText = 'Normal';
                                            $icon = 'fa-check-circle';
                                            
                                            if ($status === 'warning') {
                                                $badgeClass = 'badge' . ' bg-warning';
                                                $statusText = 'Warning';
                                                $icon = 'fa-exclamation-triangle';
                                            } elseif ($status === 'critical' || $status === 'danger') {
                                                $badgeClass = 'badge' . ' bg-danger';
                                                $statusText = 'Critical';
                                                $icon = 'fa-times-circle';
                                            } elseif ($status === 'no_data') {
                                                $badgeClass = 'badge' . ' bg-secondary';
                                                $statusText = 'No Data';
                                                $icon = 'fa-question-circle';
                                            }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <i class="fas <?= $icon ?> me-1"></i><?= $statusText ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary view-details" 
                                                data-id="<?= $reading['id'] ?? '' ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Show thresholds being used -->
                <div class="mt-4 p-3 bg-light rounded">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Current thresholds: 
                        Temp: <?= $thresholds['temp_min'] ?? 20 ?>-<?= $thresholds['temp_max'] ?? 30 ?>°C | 
                        pH: <?= $thresholds['ph_min'] ?? 6.5 ?>-<?= $thresholds['ph_max'] ?? 8.5 ?> | 
                        Turbidity: &lt;<?= $thresholds['turbidity_max'] ?? 10 ?> NTU
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reading Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Details will be loaded here via JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

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

        $(document).ready(function() {
            // Store threshold values from PHP
            var tempMin = <?= $thresholds['temp_min'] ?? 20 ?>;
            var tempMax = <?= $thresholds['temp_max'] ?? 30 ?>;
            var phMin = <?= $thresholds['ph_min'] ?? 6.5 ?>;
            var phMax = <?= $thresholds['ph_max'] ?? 8.5 ?>;
            var turbidityMax = <?= $thresholds['turbidity_max'] ?? 100 ?>;
            
            // Initialize DataTable
            var table = $('#sensorReadingsTable').DataTable({
                "order": [[0, 'desc']],
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "responsive": true,
                "dom": '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "search": "<i class='fas fa-search'></i> ",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "zeroRecords": "No matching records found",
                    "paginate": {
                        "first": "<i class='fas fa-angle-double-left'></i>",
                        "last": "<i class='fas fa-angle-double-right'></i>",
                        "next": "<i class='fas fa-angle-right'></i>",
                        "previous": "<i class='fas fa-angle-left'></i>"
                    }
                },
                "processing": true
            });

            // Function to apply all filters
            function applyFilters() {
                $.fn.dataTable.ext.search = [];
                
                var statusFilter = $('#statusFilter').val();
                var tempFilter = $('#tempFilter').val();
                var phFilter = $('#phFilter').val();
                
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var row = table.row(dataIndex).node();
                    if (!row) return true;
                    
                    var rowStatus = $(row).data('status');
                    var rowTemp = parseFloat($(row).data('temperature')) || 0;
                    var rowPh = parseFloat($(row).data('ph')) || 0;
                    
                    var statusMatch = true;
                    var tempMatch = true;
                    var phMatch = true;
                    
                    if (statusFilter && rowStatus !== statusFilter) {
                        statusMatch = false;
                    }
                    
                    if (tempFilter) {
                        switch(tempFilter) {
                            case 'low':
                                tempMatch = (rowTemp < tempMin);
                                break;
                            case 'normal':
                                tempMatch = (rowTemp >= tempMin && rowTemp <= tempMax);
                                break;
                            case 'high':
                                tempMatch = (rowTemp > tempMax);
                                break;
                        }
                    }
                    
                    if (phFilter) {
                        switch(phFilter) {
                            case 'low':
                                phMatch = (rowPh < phMin);
                                break;
                            case 'normal':
                                phMatch = (rowPh >= phMin && rowPh <= phMax);
                                break;
                            case 'high':
                                phMatch = (rowPh > phMax);
                                break;
                        }
                    }
                    
                    return statusMatch && tempMatch && phMatch;
                });
                
                table.draw();
            }

            // Filter event handlers
            $('#statusFilter').on('change', function() {
                applyFilters();
            });

            $('#tempFilter').on('change', function() {
                applyFilters();
            });

            $('#phFilter').on('change', function() {
                applyFilters();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#statusFilter, #tempFilter, #phFilter').val('');
                $.fn.dataTable.ext.search = [];
                table.draw();
            });

            // View details button
            $(document).on('click', '.view-details', function() {
                var row = $(this).closest('tr');
                var readingId = row.data('id');
                var timestamp = row.data('created-at');
                var temperature = row.data('temperature');
                var phLevel = row.data('ph');
                var turbidity = row.data('turbidity');
                var status = row.data('status');
                
                var date = new Date(timestamp);
                var formattedDate = date.toLocaleDateString('en-US', { 
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                
                var statusBadge = row.find('td:eq(4)').html();
                
                var details = `
                    <div class="row">
                        <div class="col-12 mb-3">
                            <strong><i class="fas fa-id-card" style="color: #4eac9b;"></i> Reading ID:</strong><br>
                            ${readingId || 'N/A'}
                        </div>
                        <div class="col-12 mb-3">
                            <strong><i class="fas fa-clock" style="color: #4eac9b;"></i> Timestamp:</strong><br>
                            ${formattedDate}
                        </div>
                        <div class="col-4 mb-3">
                            <strong><i class="fas fa-thermometer-half" style="color: #ff6b6b;"></i> Temperature:</strong><br>
                            <span class="h5" style="color: #4eac9b;">${parseFloat(temperature).toFixed(1)}°C</span>
                        </div>
                        <div class="col-4 mb-3">
                            <strong><i class="fas fa-tint" style="color: #4eac9b;"></i> pH Level:</strong><br>
                            <span class="h5" style="color: #4eac9b;">${parseFloat(phLevel).toFixed(2)}</span>
                        </div>
                        <div class="col-4 mb-3">
                            <strong><i class="fas fa-water" style="color: #ffc107;"></i> Turbidity:</strong><br>
                            <span class="h5" style="color: #4eac9b;">${parseInt(turbidity)} NTU</span>
                        </div>
                        <div class="col-12 mb-3">
                            <strong><i class="fas fa-info-circle" style="color: #4eac9b;"></i> Status:</strong><br>
                            ${statusBadge}
                        </div>
                    </div>
                    <hr style="border-color: rgba(78, 172, 155, 0.3);">
                    <div class="alert ${getAlertClass(status)}">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Analysis:</strong> ${getAnalysisMessage(parseFloat(temperature), parseFloat(phLevel), parseInt(turbidity))}
                    </div>
                `;
                
                $('#modalBody').html(details);
                $('#detailsModal').modal('show');
            });

            function getAlertClass(status) {
                switch(status) {
                    case 'normal':
                    case 'good':
                        return 'alert-success';
                    case 'warning':
                        return 'alert-warning';
                    case 'critical':
                    case 'danger':
                        return 'alert-danger';
                    default:
                        return 'alert-secondary';
                }
            }

            function getAnalysisMessage(temp, ph, turbidity) {
                var issues = [];
                var recommendations = [];
                
                if (temp < tempMin) {
                    issues.push('Low temperature (' + temp.toFixed(1) + '°C)');
                    recommendations.push('Consider warming the water');
                } else if (temp > tempMax) {
                    issues.push('High temperature (' + temp.toFixed(1) + '°C)');
                    recommendations.push('Consider cooling the water');
                }
                
                if (ph < phMin) {
                    issues.push('Low pH level (' + ph.toFixed(2) + ')');
                    recommendations.push('Consider adding pH increaser');
                } else if (ph > phMax) {
                    issues.push('High pH level (' + ph.toFixed(2) + ')');
                    recommendations.push('Consider adding pH decreaser');
                }
                
                if (turbidity > turbidityMax) {
                    issues.push('High turbidity (' + turbidity + ' NTU)');
                    recommendations.push('Consider water filtration or treatment');
                }
                
                if (issues.length === 0) {
                    return 'All water parameters are within normal ranges. Water quality is good.';
                } else {
                    var message = 'Detected issues: ' + issues.join(', ') + '. ';
                    if (recommendations.length > 0) {
                        message += 'Recommendations: ' + recommendations.join(', ') + '.';
                    }
                    return message;
                }
            }
        });
    </script>


<?= $this->include('layout/footer') ?>