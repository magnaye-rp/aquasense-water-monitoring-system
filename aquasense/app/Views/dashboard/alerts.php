<?= $this->include('layout/header') ?>

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
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%) !important;
            border: none !important;
            color: white !important;
        }

        .card-body {
            background: rgba(15, 38, 38, 0.5) !important;
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
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i> System Alerts
                        </div>
                        <div>
                            <button class="btn btn-light btn-sm" onclick="clearAllAlerts()">
                                <i class="fas fa-trash-alt me-1"></i> Clear All
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($alerts)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <h5 class="text-success">No alerts</h5>
                                <p class="text-muted">All systems are operating normally</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($alerts as $alert): ?>
                                    <div class="list-group-item alert-item alert-<?= $alert['level'] ?> mb-2">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-<?= $alert['level'] ?> me-2">
                                                        <?= strtoupper($alert['level']) ?>
                                                    </span>
                                                    <h6 class="mb-0"><?= ucfirst($alert['type']) ?> Alert</h6>
                                                    <small class="text-muted ms-2">
                                                        <i class="far fa-clock me-1"></i>
                                                        <?= date("M d, H:i", strtotime($alert['created_at'])) ?>
                                                    </small>
                                                </div>
                                                <p class="mb-1"><?= $alert['message'] ?></p>
                                            </div>
                                            <div class="ms-3">
                                                <a href="<?= base_url('dashboard/delete-alert/' . $alert['id']) ?>" 
                                                   class="btn btn-sm btn-outline-danger mark-read-btn"
                                                   onclick="return confirm('Are you sure you want to delete this alert?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination -->
                            <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                                <nav aria-label="Page navigation" class="mt-4">
                                    <?= $pager->links() ?>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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

        function clearAllAlerts() {
            if (confirm('Are you sure you want to clear all alerts? This action cannot be undone.')) {
                // In a real implementation, you would make an AJAX call to clear alerts
                alert('This feature would clear all alerts in a real implementation.');
                window.location.reload();
            }
        }

        // Auto-refresh every 30 seconds
        setInterval(function () {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>

<?= $this->include('layout/footer') ?>