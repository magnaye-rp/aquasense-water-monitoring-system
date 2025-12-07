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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
            color: var(--primary-color) !important;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        .sensor-value {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .sensor-unit {
            font-size: 0.9rem;
            color: var(--secondary-color);
        }
        .alert-badge {
            position: absolute;
            top: -5px;
            right: -5px;
        }
        .device-card {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .device-card:hover {
            transform: translateY(-2px);
        }
        .device-on {
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        .device-off {
            color: var(--secondary-color);
            border-left: 4px solid var(--secondary-color);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-tint text-primary me-2"></i>
                AquaSense
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('dashboard') ?>">
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
                            <?php if ($unreadAlerts > 0): ?>
                                <span class="badge bg-danger rounded-pill alert-badge"><?= $unreadAlerts ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard/devices') ?>">
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
        <!-- Status Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-<?= $status['color'] ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">
                                    <i class="fas fa-<?= $status['color'] == 'danger' ? 'exclamation-triangle' : 
                                                     ($status['color'] == 'warning' ? 'exclamation-circle' : 'check-circle') ?> 
                                                     text-<?= $status['color'] ?> me-2"></i>
                                    System Status: <?= ucfirst($status['status']) ?>
                                </h5>
                                <p class="card-text mb-0"><?= $status['message'] ?></p>
                            </div>
                            <div>
                                <span class="badge bg-<?= $status['color'] ?> status-badge">
                                    <?= strtoupper($status['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Readings -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-thermometer-half me-2"></i> Temperature
                    </div>
                    <div class="card-body text-center">
                        <?php if ($currentReading): ?>
                            <div class="sensor-value text-primary">
                                <?= number_format($currentReading['temperature'], 1) ?>
                                <span class="sensor-unit">°C</span>
                            </div>
                            <div class="mt-2">
                                <?php if ($currentReading['temperature'] < $status['thresholds']['temp_min']): ?>
                                    <span class="badge bg-warning">Low</span>
                                <?php elseif ($currentReading['temperature'] > $status['thresholds']['temp_max']): ?>
                                    <span class="badge bg-danger">High</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Normal</span>
                                <?php endif; ?>
                                <small class="text-muted">Range: <?= $status['thresholds']['temp_min'] ?>-<?= $status['thresholds']['temp_max'] ?>°C</small>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">No data</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-vial me-2"></i> pH Level
                    </div>
                    <div class="card-body text-center">
                        <?php if ($currentReading): ?>
                            <div class="sensor-value text-info">
                                <?= number_format($currentReading['ph_level'], 2) ?>
                            </div>
                            <div class="mt-2">
                                <?php if ($currentReading['ph_level'] < $status['thresholds']['ph_min']): ?>
                                    <span class="badge bg-danger">Acidic</span>
                                <?php elseif ($currentReading['ph_level'] > $status['thresholds']['ph_max']): ?>
                                    <span class="badge bg-danger">Alkaline</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Normal</span>
                                <?php endif; ?>
                                <small class="text-muted">Range: <?= $status['thresholds']['ph_min'] ?>-<?= $status['thresholds']['ph_max'] ?></small>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">No data</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-water me-2"></i> Turbidity
                    </div>
                    <div class="card-body text-center">
                        <?php if ($currentReading): ?>
                            <div class="sensor-value text-warning">
                                <?= number_format($currentReading['turbidity'], 0) ?>
                                <span class="sensor-unit">NTU</span>
                            </div>
                            <div class="mt-2">
                                <?php if ($currentReading['turbidity'] > $status['thresholds']['turbidity_max']): ?>
                                    <span class="badge bg-danger">High</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Normal</span>
                                <?php endif; ?>
                                <small class="text-muted">Max: <?= $status['thresholds']['turbidity_max'] ?> NTU</small>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">No data</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="fas fa-chart-line me-2"></i> Sensor Trends (Last 24 Hours)
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
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
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-bell me-2"></i> Recent Alerts
                        </div>
                        <a href="<?= base_url('dashboard/alerts') ?>" class="text-white text-decoration-none">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($alerts)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                                <p class="mb-0">No recent alerts</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($alerts as $alert): ?>
                                    <div class="list-group-item border-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                <span class="badge bg-<?= $alert['level'] ?> me-2">
                                                    <?= strtoupper($alert['level']) ?>
                                                </span>
                                                <?= ucfirst($alert['type']) ?>
                                            </h6>
                                            <small><?= date('H:i', strtotime($alert['created_at'])) ?></small>
                                        </div>
                                        <p class="mb-1"><?= $alert['message'] ?></p>
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
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-cogs me-2"></i> Device Control
                        </div>
                        <a href="<?= base_url('dashboard/devices') ?>" class="text-white text-decoration-none">
                            Manage <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Oxygenator -->
                            <div class="col-md-6 mb-3">
                                <div class="card device-card <?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'device-on' : 'device-off' ?>"
                                     onclick="controlDevice('oxygenator', '<?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'off' : 'on' ?>')">
                                    <div class="card-body text-center">
                                        <i class="fas fa-wind fa-3x mb-3"></i>
                                        <h5 class="card-title">Oxygenator</h5>
                                        <div class="d-flex justify-content-center align-items-center mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="oxySwitch" <?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'checked' : '' ?>
                                                       onchange="controlDevice('oxygenator', this.checked ? 'on' : 'off')">
                                                <label class="form-check-label" for="oxySwitch">
                                                    <?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                                </label>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= $deviceStatus['oxygenator']['triggered_by'] ? ucfirst($deviceStatus['oxygenator']['triggered_by']) : 'Unknown' ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Water Pump -->
                            <div class="col-md-6 mb-3">
                                <div class="card device-card <?= $deviceStatus['water_pump']['state'] == 'ON' ? 'device-on' : 'device-off' ?>"
                                     onclick="controlDevice('water_pump', '<?= $deviceStatus['water_pump']['state'] == 'ON' ? 'off' : 'on' ?>')">
                                    <div class="card-body text-center">
                                        <i class="fas fa-tint fa-3x mb-3"></i>
                                        <h5 class="card-title">Water Pump</h5>
                                        <div class="d-flex justify-content-center align-items-center mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="pumpSwitch" <?= $deviceStatus['water_pump']['state'] == 'ON' ? 'checked' : '' ?>
                                                       onchange="controlDevice('water_pump', this.checked ? 'on' : 'off')">
                                                <label class="form-check-label" for="pumpSwitch">
                                                    <?= $deviceStatus['water_pump']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                                </label>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= $deviceStatus['water_pump']['triggered_by'] ? ucfirst($deviceStatus['water_pump']['triggered_by']) : 'Unknown' ?>
                                        </small>
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Initialize Chart
        const chartData = <?= $chartData ?>;
        const ctx = document.getElementById('sensorChart').getContext('2d');
        const sensorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Temperature (°C)',
                        data: chartData.temperature,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'pH Level',
                        data: chartData.ph,
                        borderColor: '#0dcaf0',
                        backgroundColor: 'rgba(13, 202, 240, 0.1)',
                        tension: 0.3,
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Turbidity (NTU)',
                        data: chartData.turbidity,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.3,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Temperature / Turbidity'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'pH Level'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // Auto-refresh data every 10 seconds
        function refreshData() {
            $.ajax({
                url: '<?= base_url("dashboard/get-current-data") ?>',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Update current readings
                        if (response.currentReading) {
                            $('.sensor-value').each(function() {
                                const sensor = $(this).closest('.col-md-4').find('.card-header').text().trim().toLowerCase();
                                let value = '';
                                let unit = '';
                                
                                if (sensor.includes('temperature')) {
                                    value = response.currentReading.temperature.toFixed(1);
                                    unit = '°C';
                                    updateStatusBadge($(this).next().find('.badge'), 
                                                     response.currentReading.temperature, 
                                                     response.status.thresholds.temp_min, 
                                                     response.status.thresholds.temp_max);
                                } else if (sensor.includes('ph')) {
                                    value = response.currentReading.ph_level.toFixed(2);
                                    updateStatusBadge($(this).next().find('.badge'), 
                                                     response.currentReading.ph_level, 
                                                     response.status.thresholds.ph_min, 
                                                     response.status.thresholds.ph_max, 'ph');
                                } else if (sensor.includes('turbidity')) {
                                    value = response.currentReading.turbidity.toFixed(0);
                                    unit = 'NTU';
                                    updateStatusBadge($(this).next().find('.badge'), 
                                                     response.currentReading.turbidity, 
                                                     0, 
                                                     response.status.thresholds.turbidity_max, 'turbidity');
                                }
                                
                                $(this).html(value + ' <span class="sensor-unit">' + unit + '</span>');
                            });
                        }
                        
                        // Update device status
                        if (response.deviceStatus) {
                            const oxyState = response.deviceStatus.oxygenator.state;
                            const pumpState = response.deviceStatus.water_pump.state;
                            
                            $('#oxySwitch').prop('checked', oxyState === 'ON');
                            $('#oxySwitch').next('label').text(oxyState);
                            
                            $('#pumpSwitch').prop('checked', pumpState === 'ON');
                            $('#pumpSwitch').next('label').text(pumpState);
                            
                            // Update card classes
                            $('.device-card').each(function() {
                                const device = $(this).find('.card-title').text().trim().toLowerCase();
                                const state = device.includes('oxygenator') ? oxyState : pumpState;
                                
                                $(this).removeClass('device-on device-off')
                                       .addClass(state === 'ON' ? 'device-on' : 'device-off');
                            });
                        }
                        
                        // Update alert badge
                        if (response.unreadAlerts > 0) {
                            $('.alert-badge').text(response.unreadAlerts).show();
                        } else {
                            $('.alert-badge').hide();
                        }
                    }
                },
                error: function() {
                    console.log('Error refreshing data');
                }
            });
        }

        function updateStatusBadge(element, value, min, max, type = 'temperature') {
            element.removeClass('bg-success bg-warning bg-danger');
            
            if (type === 'ph') {
                if (value < min) {
                    element.text('Acidic').addClass('bg-danger');
                } else if (value > max) {
                    element.text('Alkaline').addClass('bg-danger');
                } else {
                    element.text('Normal').addClass('bg-success');
                }
            } else if (type === 'turbidity') {
                if (value > max) {
                    element.text('High').addClass('bg-danger');
                } else {
                    element.text('Normal').addClass('bg-success');
                }
            } else {
                if (value < min) {
                    element.text('Low').addClass('bg-warning');
                } else if (value > max) {
                    element.text('High').addClass('bg-danger');
                } else {
                    element.text('Normal').addClass('bg-success');
                }
            }
        }

        // Control device function
        function controlDevice(device, action) {
            if (confirm(`Are you sure you want to turn ${device} ${action}?`)) {
                $.ajax({
                    url: '<?= base_url("dashboard/control-device") ?>',
                    method: 'POST',
                    data: {
                        device: device,
                        action: action
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            refreshData();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error controlling device');
                    }
                });
            }
        }

        // Auto-refresh every 10 seconds
        setInterval(refreshData, 10000);

        // Initial refresh
        $(document).ready(function() {
            refreshData();
        });
    </script>
</body>
</html>