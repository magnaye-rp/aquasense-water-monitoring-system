<?= $this->include('layout/header') ?>

<!-- Status Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card status-banner border-<?= $status['color'] ?>">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-<?= $status['color'] == 'danger' ? 'exclamation-triangle' : 
                                         ($status['color'] == 'warning' ? 'exclamation-circle' : 'check-circle') ?> 
                                         me-3 fs-4 text-<?= $status['color'] ?>"></i>
                        <div>
                            <h5 class="card-title mb-1 text-white">System Status: <?= ucfirst($status['status']) ?></h5>
                            <p class="card-text mb-0 text-white-50"><?= $status['message'] ?></p>
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-<?= $status['color'] ?> px-3 py-2">
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
        <div class="card h-100 border-secondary">
            <div class="card-header d-flex align-items-center bg-secondary">
                <i class="fas fa-thermometer-half me-2 text-white"></i>
                <span class="text-white">Temperature</span>
            </div>
            <div class="card-body bg-dark">
                <?php if ($currentReading): ?>
                    <div class="gauge-container position-relative" style="height: 180px;">
                        <canvas id="tempGauge" height="180"></canvas>
                        <div class="gauge-value position-absolute top-50 start-50 translate-middle text-center">
                            <div class="gauge-primary-value display-5 fw-bold text-white">
                                <?= number_format($currentReading['temperature'], 1) ?>
                            </div>
                            <div class="gauge-unit text-white-50">°C</div>
                        </div>
                        <div class="gauge-threshold text-center mt-3">
                            <small class="text-white-50">
                                Range: <?= $status['thresholds']['temp_min'] ?>-<?= $status['thresholds']['temp_max'] ?>°C
                            </small>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <?php if ($currentReading['temperature'] < $status['thresholds']['temp_min']): ?>
                            <span class="badge bg-warning">Low</span>
                        <?php elseif ($currentReading['temperature'] > $status['thresholds']['temp_max']): ?>
                            <span class="badge bg-danger">High</span>
                        <?php else: ?>
                            <span class="badge bg-success">Normal</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-thermometer-empty fa-3x mb-3"></i>
                        <p class="mb-0">No temperature data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- pH Level Gauge -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-secondary">
            <div class="card-header d-flex align-items-center bg-secondary">
                <i class="fas fa-vial me-2 text-white"></i>
                <span class="text-white">pH Level</span>
            </div>
            <div class="card-body bg-dark">
                <?php if ($currentReading): ?>
                    <div class="gauge-container position-relative" style="height: 180px;">
                        <canvas id="phGauge" height="180"></canvas>
                        <div class="gauge-value position-absolute top-50 start-50 translate-middle text-center">
                            <div class="gauge-primary-value display-5 fw-bold text-white">
                                <?= number_format($currentReading['ph_level'], 2) ?>
                            </div>
                        </div>
                        <div class="gauge-threshold text-center mt-3">
                            <small class="text-white-50">
                                Range: <?= number_format($status['thresholds']['ph_min'], 1) ?>-<?= number_format($status['thresholds']['ph_max'], 1) ?>
                            </small>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <?php if ($currentReading['ph_level'] < $status['thresholds']['ph_min']): ?>
                            <span class="badge bg-danger">Acidic</span>
                        <?php elseif ($currentReading['ph_level'] > $status['thresholds']['ph_max']): ?>
                            <span class="badge bg-danger">Alkaline</span>
                        <?php else: ?>
                            <span class="badge bg-success">Normal</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-vial fa-3x mb-3"></i>
                        <p class="mb-0">No pH data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Turbidity Gauge -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-secondary">
            <div class="card-header d-flex align-items-center bg-secondary">
                <i class="fas fa-water me-2 text-white"></i>
                <span class="text-white">Turbidity</span>
            </div>
            <div class="card-body bg-dark">
                <?php if ($currentReading): ?>
                    <div class="gauge-container position-relative" style="height: 180px;">
                        <canvas id="turbidityGauge" height="180"></canvas>
                        <div class="gauge-value position-absolute top-50 start-50 translate-middle text-center">
                            <div class="gauge-primary-value display-5 fw-bold text-white">
                                <?= number_format($currentReading['turbidity'], 0) ?>
                            </div>
                            <div class="gauge-unit text-white-50">NTU</div>
                        </div>
                        <div class="gauge-threshold text-center mt-3">
                            <small class="text-white-50">
                                Max: <?= $status['thresholds']['turbidity_max'] ?> NTU
                            </small>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <?php if ($currentReading['turbidity'] > $status['thresholds']['turbidity_max']): ?>
                            <span class="badge bg-danger">High</span>
                        <?php else: ?>
                            <span class="badge bg-success">Normal</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-water fa-3x mb-3"></i>
                        <p class="mb-0">No turbidity data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-secondary">
            <div class="card-header d-flex align-items-center bg-secondary">
                <i class="fas fa-chart-line me-2 text-white"></i>
                <span class="text-white">Sensor Trends (Last 24 Hours)</span>
            </div>
            <div class="card-body bg-dark">
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
        <div class="card h-100 border-secondary">
            <div class="card-header d-flex justify-content-between align-items-center bg-secondary">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bell me-2 text-white"></i>
                    <span class="text-white">Recent Alerts</span>
                    <?php if ($unreadAlerts > 0): ?>
                        <span class="badge bg-danger ms-2"><?= $unreadAlerts ?> new</span>
                    <?php endif; ?>
                </div>
                <a href="<?= base_url('dashboard/alerts') ?>" class="text-white text-decoration-none">
                    View All <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body bg-dark">
                <?php if (empty($alerts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x mb-3 text-success opacity-50"></i>
                        <p class="text-muted mb-0">No recent alerts</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($alerts as $alert): ?>
                            <div class="alert-item border-<?= $alert['level'] ?> mb-3 p-3 rounded bg-dark">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0 fw-semibold text-white"><?= ucfirst($alert['type']) ?></h6>
                                    <small class="text-white-50"><?= date('H:i', strtotime($alert['created_at'])) ?></small>
                                </div>
                                <p class="mb-2 text-white-50"><?= htmlspecialchars($alert['message']) ?></p>
                                <span class="badge bg-<?= $alert['level'] ?>">
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
        <div class="card h-100 border-secondary">
            <div class="card-header d-flex justify-content-between align-items-center bg-secondary">
                <div class="d-flex align-items-center">
                    <i class="fas fa-cogs me-2 text-white"></i>
                    <span class="text-white">Device Control</span>
                </div>
                <a href="<?= base_url('dashboard/devices') ?>" class="text-white text-decoration-none">
                    Manage <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body bg-dark">
                <div class="row">
                    <!-- Oxygenator -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-<?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'success' : 'secondary' ?>">
                            <div class="card-body text-center py-4 bg-dark">
                                <i class="fas fa-wind fa-3x mb-3 text-<?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'success' : 'secondary' ?>"></i>
                                <h5 class="card-title mb-3 text-white">Oxygenator</h5>
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="oxySwitch" <?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'checked' : '' ?>
                                               onchange="controlDevice('oxygenator', this.checked ? 'on' : 'off')"
                                               style="width: 3em; height: 1.5em;">
                                        <label class="form-check-label ms-2 fw-medium text-white" for="oxySwitch">
                                            <?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                        </label>
                                    </div>
                                </div>
                                <small class="text-white-50">
                                    <i class="fas fa-history me-1"></i>
                                    <?php if ($deviceStatus['oxygenator']['last_updated']): ?>
                                        <?= date('H:i', strtotime($deviceStatus['oxygenator']['last_updated'])) ?>
                                    <?php else: ?>
                                        Never
                                    <?php endif; ?>
                                    <br>
                                    <i class="fas fa-user me-1"></i>
                                    <?= ucfirst($deviceStatus['oxygenator']['triggered_by']) ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Water Pump -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-<?= $deviceStatus['water_pump']['state'] == 'ON' ? 'success' : 'secondary' ?>">
                            <div class="card-body text-center py-4 bg-dark">
                                <i class="fas fa-tint fa-3x mb-3 text-<?= $deviceStatus['water_pump']['state'] == 'ON' ? 'success' : 'secondary' ?>"></i>
                                <h5 class="card-title mb-3 text-white">Water Pump</h5>
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="pumpSwitch" <?= $deviceStatus['water_pump']['state'] == 'ON' ? 'checked' : '' ?>
                                               onchange="controlDevice('water_pump', this.checked ? 'on' : 'off')"
                                               style="width: 3em; height: 1.5em;">
                                        <label class="form-check-label ms-2 fw-medium text-white" for="pumpSwitch">
                                            <?= $deviceStatus['water_pump']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                        </label>
                                    </div>
                                </div>
                                <small class="text-white-50">
                                    <i class="fas fa-history me-1"></i>
                                    <?php if ($deviceStatus['water_pump']['last_updated']): ?>
                                        <?= date('H:i', strtotime($deviceStatus['water_pump']['last_updated'])) ?>
                                    <?php else: ?>
                                        Never
                                    <?php endif; ?>
                                    <br>
                                    <i class="fas fa-user me-1"></i>
                                    <?= ucfirst($deviceStatus['water_pump']['triggered_by']) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Auto Mode Status -->
                <div class="mt-4 p-3 rounded bg-secondary">
                    <h6 class="text-white mb-2">
                        <i class="fas fa-robot me-2"></i>Auto Mode Status
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-white-50">Oxygenator:</small><br>
                            <span class="badge bg-<?= $autoModeStatus['oxygenator'] ? 'success' : 'secondary' ?>">
                                <?= $autoModeStatus['oxygenator'] ? 'ENABLED' : 'DISABLED' ?>
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-white-50">Water Pump:</small><br>
                            <span class="badge bg-<?= $autoModeStatus['pump'] ? 'success' : 'secondary' ?>">
                                <?= $autoModeStatus['pump'] ? 'ENABLED' : 'DISABLED' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$custom_scripts = "
/* === GAUGE CHARTS CONFIGURATION === */
function createGauge(chartId, value, min, max, unit = '') {
    const ctx = document.getElementById(chartId).getContext('2d');
    
    // Determine color based on value
    let color = '#28a745'; // Green (success)
    if (value < min * 0.9 || value > max * 1.1) {
        color = '#dc3545'; // Red (danger)
    } else if (value < min || value > max) {
        color = '#ffc107'; // Yellow (warning)
    }
    
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [value, max - value],
                backgroundColor: [color, '#6c757d'], // Value color, background color
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
                tooltip: { 
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return context.datasetIndex === 0 ? value + ' ' + unit : '';
                        }
                    }
                }
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

document.addEventListener('DOMContentLoaded', function() {";
if ($currentReading):
    $custom_scripts .= "
    tempGauge = createGauge('tempGauge', " . $currentReading['temperature'] . ", 
        " . $status['thresholds']['temp_min'] . ", 
        " . $status['thresholds']['temp_max'] . ", 
        '°C'
    );
    
    phGauge = createGauge('phGauge', " . $currentReading['ph_level'] . ", 
        " . $status['thresholds']['ph_min'] . ", 
        " . $status['thresholds']['ph_max'] . "
    );
    
    turbidityGauge = createGauge('turbidityGauge', " . $currentReading['turbidity'] . ", 
        0, 
        " . $status['thresholds']['turbidity_max'] . ", 
        'NTU'
    );";
endif;

$custom_scripts .= "
    
    // Initialize chart
    initSensorChart();
});

/* === SENSOR CHART === */
function initSensorChart() {
    const chartData = " . ($chartData ?: '{}') . ";
    
    if (!chartData.labels || chartData.labels.length === 0) {
        console.log('No chart data available');
        return;
    }
    
    const ctx = document.getElementById('sensorChart').getContext('2d');
    window.sensorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Temperature (°C)',
                    data: chartData.temperature,
                    borderColor: '#60D1C6',
                    backgroundColor: 'rgba(96, 209, 198, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'pH Level',
                    data: chartData.ph,
                    borderColor: '#2ab39c',
                    backgroundColor: 'rgba(42, 179, 156, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                },
                {
                    label: 'Turbidity (NTU)',
                    data: chartData.turbidity,
                    borderColor: '#0e5a51',
                    backgroundColor: 'rgba(14, 90, 81, 0.1)',
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
                    backgroundColor: 'rgba(33, 37, 41, 0.9)',
                    titleColor: '#FFFFFF',
                    bodyColor: '#E5E7EB',
                    borderColor: '#6c757d',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
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
                        color: 'rgba(255, 255, 255, 0.1)',
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

/* === UPDATE GAUGE FUNCTION === */
function updateGauge(gauge, value, min, max, unit = '') {
    if (!gauge) return;
    
    let color = '#28a745'; // Green
    if (value < min * 0.9 || value > max * 1.1) {
        color = '#dc3545'; // Red
    } else if (value < min || value > max) {
        color = '#ffc107'; // Yellow
    }
    
    gauge.data.datasets[0].data = [value, max - value];
    gauge.data.datasets[0].backgroundColor[0] = color;
    gauge.update();
    
    return color;
}

/* === REFRESH DATA FUNCTION === */
function refreshData() {
    $.ajax({
        url: '" . base_url('dashboard/get-current-data') . "',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.currentReading) {
                // Update gauges
                const tempColor = updateGauge(tempGauge, response.currentReading.temperature, 
                    response.status.thresholds.temp_min, response.status.thresholds.temp_max, '°C');
                
                const phColor = updateGauge(phGauge, response.currentReading.ph_level,
                    response.status.thresholds.ph_min, response.status.thresholds.ph_max);
                
                const turbColor = updateGauge(turbidityGauge, response.currentReading.turbidity,
                    0, response.status.thresholds.turbidity_max, 'NTU');
                
                // Update gauge text values
                $('#tempGauge').closest('.gauge-container').find('.gauge-primary-value').text(
                    response.currentReading.temperature.toFixed(1)
                );
                $('#phGauge').closest('.gauge-container').find('.gauge-primary-value').text(
                    response.currentReading.ph_level.toFixed(2)
                );
                $('#turbidityGauge').closest('.gauge-container').find('.gauge-primary-value').text(
                    response.currentReading.turbidity.toFixed(0)
                );
                
                // Update status badges
                updateStatusBadge('temperature', response.currentReading.temperature, 
                    response.status.thresholds.temp_min, response.status.thresholds.temp_max);
                updateStatusBadge('ph', response.currentReading.ph_level, 
                    response.status.thresholds.ph_min, response.status.thresholds.ph_max);
                updateStatusBadge('turbidity', response.currentReading.turbidity, 
                    0, response.status.thresholds.turbidity_max);
                
                // Update device status
                if (response.deviceStatus) {
                    updateDeviceStatus(response.deviceStatus);
                }
                
                // Update alert badge
                if (response.unreadAlerts > 0) {
                    $('.alert-badge').text(response.unreadAlerts).show();
                } else {
                    $('.alert-badge').hide();
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error refreshing data:', error);
        }
    });
}

function updateStatusBadge(type, value, min, max) {
    const card = $('#' + type + 'Gauge').closest('.card');
    const badge = card.find('.gauge-status .badge');
    
    badge.removeClass('bg-success bg-warning bg-danger');
    
    if (type === 'temperature') {
        if (value < min) {
            badge.text('Low').addClass('bg-warning');
        } else if (value > max) {
            badge.text('High').addClass('bg-danger');
        } else {
            badge.text('Normal').addClass('bg-success');
        }
    } else if (type === 'ph') {
        if (value < min) {
            badge.text('Acidic').addClass('bg-danger');
        } else if (value > max) {
            badge.text('Alkaline').addClass('bg-danger');
        } else {
            badge.text('Normal').addClass('bg-success');
        }
    } else if (type === 'turbidity') {
        if (value > max) {
            badge.text('High').addClass('bg-danger');
        } else {
            badge.text('Normal').addClass('bg-success');
        }
    }
}

function updateDeviceStatus(deviceStatus) {
    // Update oxygenator
    const oxyState = deviceStatus.oxygenator.state;
    $('#oxySwitch').prop('checked', oxyState === 'ON');
    $('#oxySwitch').next('label').text(oxyState === 'ON' ? 'ON' : 'OFF');
    $('#oxySwitch').closest('.card').removeClass('border-success border-secondary')
        .addClass(oxyState === 'ON' ? 'border-success' : 'border-secondary');
    $('#oxySwitch').closest('.card').find('.fa-wind').removeClass('text-success text-secondary')
        .addClass(oxyState === 'ON' ? 'text-success' : 'text-secondary');
    
    // Update water pump
    const pumpState = deviceStatus.water_pump.state;
    $('#pumpSwitch').prop('checked', pumpState === 'ON');
    $('#pumpSwitch').next('label').text(pumpState === 'ON' ? 'ON' : 'OFF');
    $('#pumpSwitch').closest('.card').removeClass('border-success border-secondary')
        .addClass(pumpState === 'ON' ? 'border-success' : 'border-secondary');
    $('#pumpSwitch').closest('.card').find('.fa-tint').removeClass('text-success text-secondary')
        .addClass(pumpState === 'ON' ? 'text-success' : 'text-secondary');
}

/* === DEVICE CONTROL === */
function controlDevice(device, action) {
    const button = event ? event.target : null;
    const originalHTML = button ? button.innerHTML : null;
    
    if (button) {
        button.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i>';
        button.disabled = true;
    }
    
    $.ajax({
        url: '" . base_url('dashboard/control-device') . "',
        method: 'POST',
        data: {
            device: device,
            action: action,
            csrf_test_name: $('meta[name=\"csrf-token\"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                refreshData();
            } else {
                showToast('error', response.message || 'Unknown error');
                // Revert switch state if failed
                const switchId = device === 'oxygenator' ? 'oxySwitch' : 'pumpSwitch';
                $('#' + switchId).prop('checked', !$('#' + switchId).prop('checked'));
            }
        },
        error: function(xhr, status, error) {
            showToast('error', 'Network error: ' + error);
            // Revert switch state
            const switchId = device === 'oxygenator' ? 'oxySwitch' : 'pumpSwitch';
            $('#' + switchId).prop('checked', !$('#' + switchId).prop('checked'));
        },
        complete: function() {
            if (button) {
                button.innerHTML = originalHTML;
                button.disabled = false;
            }
        }
    });
}

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-' + type + ' border-0';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    
    toast.innerHTML = \`
        <div class=\"d-flex\">
            <div class=\"toast-body\">
                <i class=\"fas \${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2\"></i>
                \${message}
            </div>
            <button type=\"button\" class=\"btn-close btn-close-white me-2 m-auto\" data-bs-dismiss=\"toast\"></button>
        </div>
    \`;
    
    document.body.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Auto-refresh every 10 seconds
setInterval(refreshData, 10000);

// Initial load
$(document).ready(function() {
    refreshData();
});
";
?>

<?= $this->include('layout/footer') ?>