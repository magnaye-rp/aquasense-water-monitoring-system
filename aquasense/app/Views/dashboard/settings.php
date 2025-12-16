<?= $this->include('layout/header') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Checkbox/Switch Styles */
        .form-check-input {
            width: 20px;
            height: 20px;
            background: rgba(78, 172, 155, 0.1) !important;
            border: 2px solid #4eac9b !important;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:hover {
            background: rgba(78, 172, 155, 0.2) !important;
        }

        .form-check-input:checked {
            background: #4eac9b !important;
            border-color: #4eac9b !important;
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
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-sliders-h me-2" style="color: #ffd700;"></i>
                        <span style="color: white; font-weight: 600;">System Settings</span>
                    </div>
                    <div class="card-body">
                        <form id="settingsForm">
                            <!-- Water Type -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Water Type</label>
                                <div class="btn-group w-100" role="group">
                                    <?php foreach ($waterTypes as $key => $label): ?>
                                        <input type="radio" class="btn-check" name="water_type" 
                                               id="water_<?= $key ?>" value="<?= $key ?>" 
                                               <?= ($settings['water_type'] ?? 'generic') == $key ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-accent" for="water_<?= $key ?>">
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- pH Settings -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="ph_min" class="form-label fw-semibold">pH Minimum</label>
                                    <input type="number" step="0.1" class="form-control" id="ph_min" 
                                           name="ph_good_min" value="<?= $settings['ph_good_min'] ?? 6.5 ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="ph_max" class="form-label fw-semibold">pH Maximum</label>
                                    <input type="number" step="0.1" class="form-control" id="ph_max" 
                                           name="ph_good_max" value="<?= $settings['ph_good_max'] ?? 7.5 ?>">
                                </div>
                            </div>

                            <!-- Turbidity Limit -->
                            <div class="mb-4">
                                <label for="turbidity" class="form-label fw-semibold">Turbidity Limit (NTU)</label>
                                <input type="number" class="form-control" id="turbidity" 
                                       name="turbidity_limit" value="<?= $settings['turbidity_limit'] ?? 10 ?>">
                                <div class="form-text">
                                    Maximum acceptable turbidity level in NTU
                                </div>
                            </div>

                            <!-- Temperature Range -->
                            <div class="mb-4">
                                <label for="temp_range" class="form-label fw-semibold">Temperature Range (°C)</label>
                                <input type="text" class="form-control" id="temp_range" 
                                       name="temperature_range" value="<?= $settings['temperature_range'] ?? '20-28' ?>">
                                <div class="form-text">
                                    Format: min-max (e.g., 20-28)
                                </div>
                            </div>

                            <!-- Alert Settings -->
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_alerts" 
                                           name="email_alerts" <?= ($settings['email_alerts'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="email_alerts">
                                        Enable Email Alerts
                                    </label>
                                </div>
                                <div class="form-text">
                                    Receive email notifications for critical alerts
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-accent px-4">
                                <i class="fas fa-save me-2"></i> Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-info-circle me-2" style="color: #ffd700;"></i>
                        <span style="color: white; font-weight: 600;">Recommended Settings</span>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3" style="color: #4eac9b;">Freshwater Aquariums:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong style="color: white;">pH:</strong> <span style="color: #b0c4be;">6.5 - 7.5</span>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong style="color: white;">Temperature:</strong> <span style="color: #b0c4be;">22°C - 26°C</span>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong style="color: white;">Turbidity:</strong> <span style="color: #b0c4be;">Below 10 NTU</span>
                            </li>
                        </ul>

                        <h6 class="fw-semibold mb-3 mt-4" style="color: #4eac9b;">Saltwater Aquariums:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong style="color: white;">pH:</strong> <span style="color: #b0c4be;">8.0 - 8.4</span>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong style="color: white;">Temperature:</strong> <span style="color: #b0c4be;">24°C - 28°C</span>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong style="color: white;">Turbidity:</strong> <span style="color: #b0c4be;">Below 5 NTU</span>
                            </li>
                        </ul>
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

        // Handle form submission
        $('#settingsForm').submit(function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = $(this).serialize();
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...');
            submitBtn.prop('disabled', true);
            
            // Send AJAX request
            $.ajax({
                url: '<?= base_url('dashboard/update-settings') ?>',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        let errorMessage = 'Failed to save settings';
                        if (response.errors) {
                            errorMessage = Object.values(response.errors).join('<br>');
                        }
                        showAlert('danger', errorMessage);
                    }
                },
                error: function() {
                    showAlert('danger', 'Network error. Please try again.');
                },
                complete: function() {
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }
            });
        });

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show position-fixed';
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>

<?= $this->include('layout/footer') ?>