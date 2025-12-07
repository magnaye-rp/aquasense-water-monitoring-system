<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
            color: #0d6efd !important;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
        }
        .device-card {
            transition: all 0.3s;
            cursor: pointer;
        }
        .device-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .device-on {
            border-left: 5px solid #198754;
        }
        .device-off {
            border-left: 5px solid #dc3545;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #198754;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .table th {
            border-top: none;
            font-weight: 600;
        }
        .manual-badge {
            background-color: #0dcaf0;
        }
        .auto-badge {
            background-color: #198754;
        }
        .mode-toggle {
            border-radius: 20px;
            padding: 8px 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
                <i class="fas fa-tint text-primary me-2"></i>
                AquaSense
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard') ?>">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard/sensor-data') ?>">
                            <i class="fas fa-chart-line me-1"></i> Sensor Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard/alerts') ?>">
                            <i class="fas fa-bell me-1"></i> Alerts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('dashboard/devices') ?>">
                            <i class="fas fa-cogs me-1"></i> Device Control
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard/settings') ?>">
                            <i class="fas fa-sliders-h me-1"></i> Settings
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= $user->username ?? 'User' ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <!-- Current Device Status -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-plug me-2"></i> Current Device Status
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Oxygenator -->
                            <div class="col-md-6 mb-4">
                                <div class="card device-card h-100 <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'device-on' : 'device-off' ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-wind fa-2x me-3 <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'text-success' : 'text-secondary' ?>"></i>
                                            <div>
                                                <h5 class="card-title mb-1">Oxygenator</h5>
                                                <p class="card-text text-muted mb-0">
                                                    <small>Last updated: <?= $currentStatus['oxygenator']['last_updated'] ? date('M d, H:i', strtotime($currentStatus['oxygenator']['last_updated'])) : 'Never' ?></small>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <span class="badge <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'bg-success' : 'bg-danger' ?> status-badge">
                                                    <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'ACTIVE' : 'INACTIVE' ?>
                                                </span>
                                                <span class="badge <?= $currentStatus['oxygenator']['triggered_by'] == 'manual' ? 'manual-badge' : 'auto-badge' ?> ms-2">
                                                    <?= strtoupper($currentStatus['oxygenator']['triggered_by'] ?? 'UNKNOWN') ?>
                                                </span>
                                            </div>
                                            <label class="switch">
                                                <input type="checkbox" 
                                                       id="oxyToggle" 
                                                       <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'checked' : '' ?>
                                                       onchange="toggleDevice('oxygenator', this.checked)">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success" onclick="controlDevice('oxygenator', 'on')">
                                                <i class="fas fa-play me-2"></i> Turn ON
                                            </button>
                                            <button class="btn btn-danger" onclick="controlDevice('oxygenator', 'off')">
                                                <i class="fas fa-stop me-2"></i> Turn OFF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Water Pump -->
                            <div class="col-md-6 mb-4">
                                <div class="card device-card h-100 <?= $currentStatus['water_pump']['state'] == 'ON' ? 'device-on' : 'device-off' ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-tint fa-2x me-3 <?= $currentStatus['water_pump']['state'] == 'ON' ? 'text-success' : 'text-secondary' ?>"></i>
                                            <div>
                                                <h5 class="card-title mb-1">Water Pump</h5>
                                                <p class="card-text text-muted mb-0">
                                                    <small>Last updated: <?= $currentStatus['water_pump']['last_updated'] ? date('M d, H:i', strtotime($currentStatus['water_pump']['last_updated'])) : 'Never' ?></small>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <span class="badge <?= $currentStatus['water_pump']['state'] == 'ON' ? 'bg-success' : 'bg-danger' ?> status-badge">
                                                    <?= $currentStatus['water_pump']['state'] == 'ON' ? 'ACTIVE' : 'INACTIVE' ?>
                                                </span>
                                                <span class="badge <?= $currentStatus['water_pump']['triggered_by'] == 'manual' ? 'manual-badge' : 'auto-badge' ?> ms-2">
                                                    <?= strtoupper($currentStatus['water_pump']['triggered_by'] ?? 'UNKNOWN') ?>
                                                </span>
                                            </div>
                                            <label class="switch">
                                                <input type="checkbox" 
                                                       id="pumpToggle" 
                                                       <?= $currentStatus['water_pump']['state'] == 'ON' ? 'checked' : '' ?>
                                                       onchange="toggleDevice('water_pump', this.checked)">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success" onclick="controlDevice('water_pump', 'on')">
                                                <i class="fas fa-play me-2"></i> Turn ON
                                            </button>
                                            <button class="btn btn-danger" onclick="controlDevice('water_pump', 'off')">
                                                <i class="fas fa-stop me-2"></i> Turn OFF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Control Modes -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn <?= $settings['oxygenator_auto'] == 1 ? 'btn-primary' : 'btn-outline-primary' ?> mode-toggle"
                                                onclick="setAutoMode('oxygenator', true)">
                                            <i class="fas fa-robot me-2"></i> Auto Mode
                                        </button>
                                        <button type="button" 
                                                class="btn <?= $settings['oxygenator_auto'] == 0 ? 'btn-primary' : 'btn-outline-primary' ?> mode-toggle"
                                                onclick="setAutoMode('oxygenator', false)">
                                            <i class="fas fa-hand-pointer me-2"></i> Manual Mode
                                        </button>
                                    </div>
                                    <div class="btn-group ms-3" role="group">
                                        <button type="button" 
                                                class="btn <?= $settings['pump_auto'] == 1 ? 'btn-primary' : 'btn-outline-primary' ?> mode-toggle"
                                                onclick="setAutoMode('pump', true)">
                                            <i class="fas fa-robot me-2"></i> Auto Mode
                                        </button>
                                        <button type="button" 
                                                class="btn <?= $settings['pump_auto'] == 0 ? 'btn-primary' : 'btn-outline-primary' ?> mode-toggle"
                                                onclick="setAutoMode('pump', false)">
                                            <i class="fas fa-hand-pointer me-2"></i> Manual Mode
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Logs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-history me-2"></i> Device Activity Logs
                        </div>
                        <button class="btn btn-light btn-sm" onclick="refreshLogs()">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($deviceHistory)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-history fa-2x mb-3"></i>
                                <p class="mb-0">No device activity logs found</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Device</th>
                                            <th>Action</th>
                                            <th>Triggered By</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($deviceHistory as $log): ?>
                                            <tr>
                                                <td><?= date('M d, H:i:s', strtotime($log['created_at'])) ?></td>
                                                <td>
                                                    <span class="badge <?= $log['device_name'] == 'oxygenator' ? 'bg-info' : 'bg-primary' ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $log['device_name'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $log['action'] == 'ON' ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= $log['action'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $log['triggered_by'] == 'auto' ? 'auto-badge' : 'manual-badge' ?>">
                                                        <?= ucfirst($log['triggered_by']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $timeAgo = time() - strtotime($log['created_at']);
                                                    if ($timeAgo < 60) {
                                                        echo 'Just now';
                                                    } elseif ($timeAgo < 3600) {
                                                        echo floor($timeAgo / 60) . ' minutes ago';
                                                    } elseif ($timeAgo < 86400) {
                                                        echo floor($timeAgo / 3600) . ' hours ago';
                                                    } else {
                                                        echo floor($timeAgo / 86400) . ' days ago';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($pager->getPageCount() > 1): ?>
                                <nav aria-label="Page navigation" class="mt-3">
                                    <?= $pager->links() ?>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Device control functions
        function toggleDevice(device, state) {
            const action = state ? 'on' : 'off';
            controlDevice(device, action);
        }

        function controlDevice(device, action) {
            if (!confirm(`Are you sure you want to turn ${device} ${action}?`)) {
                // Reset toggle if cancelled
                if (device === 'oxygenator') {
                    $('#oxyToggle').prop('checked', !$('#oxyToggle').prop('checked'));
                } else {
                    $('#pumpToggle').prop('checked', !$('#pumpToggle').prop('checked'));
                }
                return;
            }

            $.ajax({
                url: '<?= base_url("dashboard/control-device") ?>',
                method: 'POST',
                data: {
                    device: device,
                    action: action,
                    csrf_test_name: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        // Update UI
                        const badge = device === 'oxygenator' ? 
                            $('#oxyToggle').closest('.device-card').find('.status-badge') :
                            $('#pumpToggle').closest('.device-card').find('.status-badge');
                        
                        badge.removeClass('bg-success bg-danger')
                             .addClass(action === 'on' ? 'bg-success' : 'bg-danger')
                             .text(action === 'on' ? 'ACTIVE' : 'INACTIVE');
                        
                        // Update card border
                        const card = device === 'oxygenator' ? 
                            $('#oxyToggle').closest('.device-card') :
                            $('#pumpToggle').closest('.device-card');
                        
                        card.removeClass('device-on device-off')
                            .addClass(action === 'on' ? 'device-on' : 'device-off');
                        
                        // Update icon color
                        const icon = card.find('.fa-2x');
                        icon.removeClass('text-success text-secondary')
                            .addClass(action === 'on' ? 'text-success' : 'text-secondary');
                    } else {
                        alert('Error: ' + response.message);
                        // Reset toggle on error
                        if (device === 'oxygenator') {
                            $('#oxyToggle').prop('checked', !$('#oxyToggle').prop('checked'));
                        } else {
                            $('#pumpToggle').prop('checked', !$('#pumpToggle').prop('checked'));
                        }
                    }
                },
                error: function() {
                    alert('Error controlling device. Please try again.');
                    // Reset toggle on error
                    if (device === 'oxygenator') {
                        $('#oxyToggle').prop('checked', !$('#oxyToggle').prop('checked'));
                    } else {
                        $('#pumpToggle').prop('checked', !$('#pumpToggle').prop('checked'));
                    }
                }
            });
        }

        function setAutoMode(device, autoMode) {
            const mode = autoMode ? 'auto' : 'manual';
            const deviceType = device === 'oxygenator' ? 'oxygenator_auto' : 'pump_auto';
            const value = autoMode ? 1 : 0;
            
            $.ajax({
                url: '<?= base_url("dashboard/update-settings") ?>',
                method: 'POST',
                data: {
                    [deviceType]: value,
                    csrf_test_name: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(`${device.charAt(0).toUpperCase() + device.slice(1)} mode set to ${mode}`);
                        // Update button styles
                        $(`button:contains("Auto Mode")`).each(function() {
                            if ($(this).text().includes('Auto') && $(this).closest('.btn-group').find('button').index($(this)) === 0) {
                                $(this).removeClass('btn-outline-primary btn-primary')
                                       .addClass(autoMode ? 'btn-primary' : 'btn-outline-primary');
                                $(this).next('button').removeClass('btn-outline-primary btn-primary')
                                       .addClass(!autoMode ? 'btn-primary' : 'btn-outline-primary');
                            }
                        });
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating mode');
                }
            });
        }

        function refreshLogs() {
            window.location.reload();
        }

        // Auto-refresh every 30 seconds
        setInterval(function() {
            refreshLogs();
        }, 30000);
    </script>
</body>
</html>