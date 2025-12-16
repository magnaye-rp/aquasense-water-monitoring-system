<?= $this->include('layout/header') ?>

<!-- Status Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card status-banner <?= $status['color'] ?>">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-<?= $status['color'] == 'danger' ? 'exclamation-triangle' : 
                                         ($status['color'] == 'warning' ? 'exclamation-circle' : 'check-circle') ?> 
                                         me-3 fs-4 <?= $status['color'] == 'danger' ? 'text-danger' : 
                                                    ($status['color'] == 'warning' ? 'text-warning' : 'text-accent') ?>"></i>
                        <div>
                            <h5 class="card-title mb-1">System Status: <?= ucfirst($status['status']) ?></h5>
                            <p class="card-text mb-0 text-muted"><?= $status['message'] ?></p>
                        </div>
                    </div>
                    <div>
                        <span class="status-badge <?= $status['color'] ?>">
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
                <i class="fas fa-thermometer-half me-2 text-accent"></i>
                <span>Temperature</span>
            </div>
            <div class="card-body">
                <?php if ($currentReading): ?>
                    <div class="gauge-container">
                        <canvas id="tempGauge"></canvas>
                        <div class="gauge-value">
                            <div class="gauge-primary-value">
                                <?= number_format($currentReading['temperature'], 1) ?>
                            </div>
                            <div class="gauge-unit">°C</div>
                        </div>
                        <div class="gauge-threshold text-center mt-2">
                            Range: <?= $status['thresholds']['temp_min'] ?>-<?= $status['thresholds']['temp_max'] ?>°C
                        </div>
                    </div>
                    <div class="text-center gauge-status">
                        <?php if ($currentReading['temperature'] < $status['thresholds']['temp_min']): ?>
                            <span class="badge bg-warning">Low</span>
                        <?php elseif ($currentReading['temperature'] > $status['thresholds']['temp_max']): ?>
                            <span class="badge bg-danger">High</span>
                        <?php else: ?>
                            <span class="badge status-badge">Normal</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">No data available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- pH Level Gauge -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-vial me-2 text-accent"></i>
                <span>pH Level</span>
            </div>
            <div class="card-body">
                <?php if ($currentReading): ?>
                    <div class="gauge-container">
                        <canvas id="phGauge"></canvas>
                        <div class="gauge-value">
                            <div class="gauge-primary-value">
                                <?= number_format($currentReading['ph_level'], 2) ?>
                            </div>
                        </div>
                        <div class="gauge-threshold text-center mt-2">
                            Range: <?= $status['thresholds']['ph_min'] ?>-<?= $status['thresholds']['ph_max'] ?>
                        </div>
                    </div>
                    <div class="text-center gauge-status">
                        <?php if ($currentReading['ph_level'] < $status['thresholds']['ph_min']): ?>
                            <span class="badge bg-danger">Acidic</span>
                        <?php elseif ($currentReading['ph_level'] > $status['thresholds']['ph_max']): ?>
                            <span class="badge bg-danger">Alkaline</span>
                        <?php else: ?>
                            <span class="badge status-badge">Normal</span>
                        <?php endif; ?>
                        </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">No data available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Turbidity Gauge -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-water me-2 text-accent"></i>
                <span>Turbidity</span>
            </div>
            <div class="card-body">
                <?php if ($currentReading): ?>
                    <div class="gauge-container">
                        <canvas id="turbidityGauge"></canvas>
                        <div class="gauge-value">
                            <div class="gauge-primary-value">
                                <?= number_format($currentReading['turbidity'], 0) ?>
                            </div>
                            <div class="gauge-unit">NTU</div>
                        </div>
                        <div class="gauge-threshold text-center mt-2">
                            Max: <?= $status['thresholds']['turbidity_max'] ?> NTU
                        </div>
                    </div>
                    <div class="text-center gauge-status">
                        <?php if ($currentReading['turbidity'] > $status['thresholds']['turbidity_max']): ?>
                            <span class="badge bg-danger">High</span>
                        <?php else: ?>
                            <span class="badge status-badge">Normal</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">No data available</div>
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
                <i class="fas fa-chart-line me-2 text-accent"></i>
                <span>Sensor Trends (Last 24 Hours)</span>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bell me-2 text-accent"></i>
                    <span>Recent Alerts</span>
                </div>
                <a href="<?= base_url('dashboard/alerts') ?>" class="text-accent text-decoration-none">
                    View All <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($alerts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x mb-3 text-accent opacity-50"></i>
                        <p class="text-muted mb-0">No recent alerts</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($alerts as $alert): ?>
                            <div class="alert-item <?= $alert['level'] ?> mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-semibold"><?= ucfirst($alert['type']) ?></h6>
                                    <small class="text-muted"><?= date('H:i', strtotime($alert['created_at'])) ?></small>
                                </div>
                                <p class="mb-2"><?= $alert['message'] ?></p>
                                <span class="badge status-badge <?= $alert['level'] ?>">
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
                    <i class="fas fa-cogs me-2 text-accent"></i>
                    <span>Device Control</span>
                </div>
                <a href="<?= base_url('dashboard/devices') ?>" class="text-accent text-decoration-none">
                    Manage <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Oxygenator -->
                    <div class="col-md-6 mb-3">
                        <div class="card device-card h-100 <?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'device-on' : 'device-off' ?>"
                             onclick="controlDevice('oxygenator', '<?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'off' : 'on' ?>')">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-wind device-icon"></i>
                                <h5 class="card-title mb-3">Oxygenator</h5>
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="oxySwitch" <?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'checked' : '' ?>
                                               onchange="controlDevice('oxygenator', this.checked ? 'on' : 'off')">
                                        <label class="form-check-label ms-2 fw-medium" for="oxySwitch">
                                            <?= $deviceStatus['oxygenator']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    Triggered by: <?= $deviceStatus['oxygenator']['triggered_by'] ? ucfirst($deviceStatus['oxygenator']['triggered_by']) : 'Unknown' ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Water Pump -->
                    <div class="col-md-6 mb-3">
                        <div class="card device-card h-100 <?= $deviceStatus['water_pump']['state'] == 'ON' ? 'device-on' : 'device-off' ?>"
                             onclick="controlDevice('water_pump', '<?= $deviceStatus['water_pump']['state'] == 'ON' ? 'off' : 'on' ?>')">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-tint device-icon"></i>
                                <h5 class="card-title mb-3">Water Pump</h5>
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="pumpSwitch" <?= $deviceStatus['water_pump']['state'] == 'ON' ? 'checked' : '' ?>
                                               onchange="controlDevice('water_pump', this.checked ? 'on' : 'off')">
                                        <label class="form-check-label ms-2 fw-medium" for="pumpSwitch">
                                            <?= $deviceStatus['water_pump']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    Triggered by: <?= $deviceStatus['water_pump']['triggered_by'] ? ucfirst($deviceStatus['water_pump']['triggered_by']) : 'Unknown' ?>
                                </small>
                            </div>
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
function createGauge(chartId, value, min, max, color) {
    const ctx = document.getElementById(chartId).getContext('2d');

    const gaugeColor = color; // Use passed color (matches chart line)

    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [value, max - value],
                backgroundColor: [gaugeColor, '#053835ff'],
                borderWidth: 0,
                circumference: 180,
                rotation: 270,
                borderRadius: 10,
                cutout: '80%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });
}

/* === INITIALIZE GAUGES === */
// Pass the colors as you specified
document.addEventListener('DOMContentLoaded', function() {";

if ($currentReading):
    $custom_scripts .= "
    createGauge('tempGauge', " . $currentReading['temperature'] . ",
        " . $status['thresholds']['temp_min'] . ",
        " . $status['thresholds']['temp_max'] . ",
        '#6de5d9ff'  // gauge1 / line1
    );

    createGauge('phGauge', " . $currentReading['ph_level'] . ",
        " . $status['thresholds']['ph_min'] . ",
        " . $status['thresholds']['ph_max'] . ",
        '#249B87'  // gauge2 / line2
    );

    createGauge('turbidityGauge', " . $currentReading['turbidity'] . ",
        0,
        " . $status['thresholds']['turbidity_max'] . ",
        '#13766B'  // gauge3 / line3
    );";
endif;

$custom_scripts .= "
});

/* === UPDATED LINE CHART CONFIG === */
const chartData = " . $chartData . ";
const ctx = document.getElementById('sensorChart').getContext('2d');
const sensorChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: [
            {
                label: 'Temperature (°C)',
                data: chartData.temperature,
                borderColor: '#60D1C6', // line1
                fill: false,
                borderWidth: 2,
                tension: 0.3,
                yAxisID: 'y'
            },
            {
                label: 'pH Level',
                data: chartData.ph,
                borderColor: '#2ab39cff', // line2
                fill: false,
                borderWidth: 2,
                tension: 0.3,
                yAxisID: 'y1'
            },
            {
                label: 'Turbidity (NTU)',
                data: chartData.turbidity,
                borderColor: '#0e5a51ff', // line3
                fill: false,
                borderWidth: 2,
                tension: 0.3,
                yAxisID: 'y'
            }
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#ffffff',
                    usePointStyle: true,
                    padding: 20,
                    font: {
                        family: '\"Inter\", \"Segoe UI\", sans-serif'
                    }
                }
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: 'var(--bg-card)',
                titleColor: '#FFFFFF',  // White title text
                bodyColor: '#E5E7EB',
                borderColor: 'var(--accent)',
                borderWidth: 1,
                padding: 12,
                titleFont: {
                    family: '\"Inter\", \"Segoe UI\", sans-serif',
                    weight: '600',
                    size: 13
                },
                bodyFont: {
                    family: '\"Inter\", \"Segoe UI\", sans-serif',
                    size: 12
                },
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            // Format values based on dataset
                            if (context.dataset.label.includes('Temperature')) {
                                label += context.parsed.y.toFixed(1) + '°C';
                            } else if (context.dataset.label.includes('pH')) {
                                label += context.parsed.y.toFixed(2);
                            } else if (context.dataset.label.includes('Turbidity')) {
                                label += context.parsed.y.toFixed(0) + ' NTU';
                            } else {
                                label += context.parsed.y.toFixed(2);
                            }
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    color: 'rgba(158, 158, 158, 0.1)',
                    drawBorder: false
                },
                ticks: {
                    color: '#ffffff',
                    font: {
                        family: '\"Inter\", \"Segoe UI\", sans-serif'
                    }
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                grid: {
                    color: 'rgba(158, 158, 158, 0.1)',
                    drawBorder: false
                },
                ticks: {
                    color: '#ffffff',
                    font: {
                        family: '\"Inter\", \"Segoe UI\", sans-serif'
                    }
                },
                title: {
                    display: true,
                    text: 'Temperature / Turbidity',
                    color: '#ffffff',
                    font: {
                        family: '\"Inter\", \"Segoe UI\", sans-serif',
                        size: 12
                    }
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
                    color: '#ffffff',
                    font: {
                        family: '\"Inter\", \"Segoe UI\", sans-serif'
                    }
                },
                title: {
                    display: true,
                    text: 'pH Level',
                    color: '#ffffff',
                    font: {
                        family: '\"Inter\", \"Segoe UI\", sans-serif',
                        size: 12
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

/* === HELPER FUNCTIONS === */
function updateGauge(chartId, value, min, max) {
    const chart = Chart.getChart(chartId);
    if (chart) {
        chart.data.datasets[0].data = [value, max - value];
        
        // Update color based on value
        if (value < min || value > max) {
            chart.data.datasets[0].backgroundColor[0] = 'var(--status-critical)';
        } else {
            chart.data.datasets[0].backgroundColor[0] = 'var(--accent-primary)';
        }
        
        chart.update('active');
    }
}

/* === UPDATED REFRESH FUNCTION FOR GAUGES === */
function refreshData() {
    $.ajax({
        url: '" . base_url('dashboard/get-current-data') . "',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Update gauges
                if (response.currentReading) {
                    // Temperature
                    updateGauge(
                        'tempGauge',
                        response.currentReading.temperature,
                        response.status.thresholds.temp_min,
                        response.status.thresholds.temp_max
                    );
                    
                    // pH Level
                    updateGauge(
                        'phGauge',
                        response.currentReading.ph_level,
                        response.status.thresholds.ph_min,
                        response.status.thresholds.ph_max
                    );
                    
                    // Turbidity
                    updateGauge(
                        'turbidityGauge',
                        response.currentReading.turbidity,
                        0,
                        response.status.thresholds.turbidity_max
                    );
                    
                    // Update gauge values display
                    document.querySelector('#tempGauge').nextElementSibling.querySelector('.gauge-primary-value').textContent = 
                        response.currentReading.temperature.toFixed(1);
                    document.querySelector('#phGauge').nextElementSibling.querySelector('.gauge-primary-value').textContent = 
                        response.currentReading.ph_level.toFixed(2);
                    document.querySelector('#turbidityGauge').nextElementSibling.querySelector('.gauge-primary-value').textContent = 
                        response.currentReading.turbidity.toFixed(0);
                    
                    // Update status badges
                    updateStatusBadge('temperature', response.currentReading.temperature, response.status.thresholds.temp_min, response.status.thresholds.temp_max);
                    updateStatusBadge('ph', response.currentReading.ph_level, response.status.thresholds.ph_min, response.status.thresholds.ph_max);
                    updateStatusBadge('turbidity', response.currentReading.turbidity, 0, response.status.thresholds.turbidity_max);
                }
                
                // Update device status (existing logic)
                if (response.deviceStatus) {
                    const oxyState = response.deviceStatus.oxygenator.state;
                    const pumpState = response.deviceStatus.water_pump.state;
                    
                    $('#oxySwitch').prop('checked', oxyState === 'ON');
                    $('#oxySwitch').next('label').text(oxyState);
                    
                    $('#pumpSwitch').prop('checked', pumpState === 'ON');
                    $('#pumpSwitch').next('label').text(pumpState);
                    
                    $('.device-card').each(function() {
                        const device = \$(this).find('.card-title').text().trim().toLowerCase();
                        const state = device.includes('oxygenator') ? oxyState : pumpState;
                        
                        \$(this).removeClass('device-on device-off')
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

function updateStatusBadge(type, value, min, max) {
    const gaugeElement = document.querySelector('#' + type + 'Gauge').closest('.card-body');
    const badgeElement = gaugeElement.querySelector('.gauge-status .badge');
    
    if (!badgeElement) return;
    
    badgeElement.className = 'badge ';
    
    if (type === 'ph') {
        if (value < min) {
            badgeElement.textContent = 'Acidic';
            badgeElement.className = 'badge danger';
        } else if (value > max) {
            badgeElement.textContent = 'Alkaline';
            badgeElement.className = 'badge danger';
        } else {
            badgeElement.textContent = 'Normal';
            badgeElement.className = 'badge status-badge';
        }
    } else if (type === 'turbidity') {
        if (value > max) {
            badgeElement.textContent = 'High';
            badgeElement.className = 'badge danger';
        } else {
            badgeElement.textContent = 'Normal';
            badgeElement.className = 'badge status-badge';
        }
    } else {
        if (value < min) {
            badgeElement.textContent = 'Low';
            badgeElement.className = 'badge warning';
        } else if (value > max) {
            badgeElement.textContent = 'High';
            badgeElement.className = 'badge danger';
        } else {
            badgeElement.textContent = 'Normal';
            badgeElement.className = 'badge status-badge';
        }
    }
}

// Keep all existing AJAX and control functions unchanged
function controlDevice(device, action) {
    console.log('Attempting to control: ' + device + ' -> ' + action);
    
    const button = event ? event.target.closest('button') : null;
    if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i> Processing...';
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
        success: function(response) {
            console.log('Server response:', response);
            
            if (response.success) {
                showAlert('success', response.message);
                updateDeviceUI(device, action);
                refreshData();
            } else {
                showAlert('error', 'Error: ' + response.message);
            }
            
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showAlert('error', 'Network error. Check console for details.');
            
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <strong>\${type === 'success' ? 'Success!' : 'Error!'}</strong> \${message}
        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function updateDeviceUI(device, action) {
    const switchId = device === 'oxygenator' ? 'oxySwitch' : 'pumpSwitch';
    const switchElement = document.getElementById(switchId);
    if (switchElement) {
        switchElement.checked = action === 'on';
    }
    
    const card = document.querySelector('.device-card:contains(' + (device === 'oxygenator' ? 'Oxygenator' : 'Water Pump') + ')');
    if (card) {
        card.classList.remove('device-on', 'device-off');
        card.classList.add(action === 'on' ? 'device-on' : 'device-off');
    }
}

// Auto-refresh every 10 seconds
setInterval(refreshData, 10000);

// Initial refresh
\$(document).ready(function() {
    refreshData();
});
";
?>

<?= $this->include('layout/footer') ?>