<?= $this->extend('layout/header') ?>

<?= $this->section('content') ?>
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
                            <div class="gauge-primary-value display-5 fw-bold mt-5" style="color: #4eac9b;">
                                <?= number_format($currentReading['temperature'], 1) ?>
                            </div>
                            <div class="gauge-unit" style="color: #b0c4be;">°C</div>
                        </div>
                        <div class="gauge-threshold text-center">
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
                            <div class="gauge-primary-value display-5 fw-bold mt-5" style="color: #4eac9b;">
                                <?= number_format($currentReading['ph_level'], 2) ?>
                            </div>
                        </div>
                        <div class="gauge-threshold text-center">
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
                            <div class="gauge-primary-value display-5 fw-bold mt-5" style="color: #4eac9b;">
                                <?= number_format($currentReading['turbidity'], 0) ?>
                            </div>
                            <div class="gauge-unit" style="color: #b0c4be;">NTU</div>
                        </div>
                        <div class="gauge-threshold text-center">
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-line me-2" style="color: #ffd700;"></i>
                    <span id="chartTitle" style="color: white; font-weight: 600;">Sensor Trends (Last 24 Hours)</span>
                </div>
                <div class="d-flex align-items-center">
                    <label for="chartRange" class="text-white me-2 mb-0" style="font-weight: 500;">Time Range:</label>
                    <select id="chartRange" class="form-select form-select-sm" style="background: rgba(78, 172, 155, 0.1); border: 2px solid #4eac9b; color: white; width: auto; min-width: 120px;">
                        <option value="24h" selected>24 Hours</option>
                        <option value="48h">48 Hours</option>
                        <option value="72h">72 Hours</option>
                        <option value="7d">7 Days</option>
                        <option value="30d">30 Days</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div id="chartLoading" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status" style="color: #4eac9b !important;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 mb-0" style="color: #b0c4be;">Loading chart data...</p>
                </div>
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
                    <h5 class="mb-0" style="color: white;">Device Control</h5>
                </div>
                <a href="<?= base_url('dashboard/devices') ?>" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-external-link-alt me-1"></i> Manage
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-4 border border-secondary rounded" style="background: rgba(78, 172, 155, 0.1);">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-wind me-5" style="font-size: 2.5rem; color: #66CFC4;"></i>
                                <div>
                                    <h4 class="mb-2 fw-bold" style="color: #66CFC4;">Oxygenator</h4>
                                    <span class="text-white fw-normal">
                                        <i style="color: #66CFC4;" class="far fa-clock me-1"></i>
                                        Last Update:
                                        <span class="fw-medium" style="color: #66CFC4;">
                                            <?= $deviceStatus['oxygenator']['last_updated'] ?
                                                date('M j, g:i A', strtotime($deviceStatus['oxygenator']['last_updated'])) :
                                                'Never' ?>
                                        </span>
                                    </span>
                                    <br>
                                    <span class="text-white fw-normal">
                                        <i class="fas fa-user me-1"></i>
                                        Triggered By:
                                        <span class="fw-medium" style="color: #66CFC4;">
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
                    
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-4 border border-secondary rounded" style="background: rgba(78, 172, 155, 0.1);">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tint me-5" style="font-size: 2.5rem; color: #66CFC4;"></i>
                                <div>
                                    <h4 class="mb-2 fw-bold" style="color: #66CFC4;">Water Pump</h4>
                                    <span class="text-white fw-normal">
                                        <i style="color: #66CFC4;" class="far fa-clock me-1"></i>
                                        Last Update:
                                        <span class="fw-medium" style="color: #66CFC4;">
                                            <?= $deviceStatus['water_pump']['last_updated'] ?
                                                date('M j, g:i A', strtotime($deviceStatus['water_pump']['last_updated'])) :
                                                'Never' ?>
                                        </span>
                                    </span>
                                    <br>
                                    <span class="text-white fw-normal">
                                        <i class="fas fa-user me-1"></i>
                                        Triggered By:
                                        <span class="fw-medium" style="color: #66CFC4;">
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

<!-- Custom JavaScript for Dashboard -->
<script>
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
        // Check if we have currentReading data
        <?php if ($currentReading): ?>
        tempGauge = createGauge('tempGauge', <?= $currentReading['temperature'] ?>, 
            <?= $status['thresholds']['temp_min'] ?>, <?= $status['thresholds']['temp_max'] ?>, '°C');
        phGauge = createGauge('phGauge', <?= $currentReading['ph_level'] ?>, 
            <?= $status['thresholds']['ph_min'] ?>, <?= $status['thresholds']['ph_max'] ?>);
        turbidityGauge = createGauge('turbidityGauge', <?= $currentReading['turbidity'] ?>, 
            0, <?= $status['thresholds']['turbidity_max'] ?>, 'NTU');
        <?php endif; ?>
        
        // Initialize chart with default range
        initSensorChart('24h');
    });

    /* === SENSOR CHART === */
    let rangeFilterInitialized = false;
    window.sensorChart = null; // Initialize chart variable
    
    function initSensorChart(range = '24h') {
        loadChartData(range);
        
        // Add event listener for range filter (only once)
        if (!rangeFilterInitialized) {
            const rangeSelect = document.getElementById('chartRange');
            if (rangeSelect) {
                rangeSelect.addEventListener('change', function() {
                    const selectedRange = this.value;
                    loadChartData(selectedRange);
                });
                rangeFilterInitialized = true;
            }
        }
    }

    function loadChartData(range = '24h') {
        const chartContainer = document.querySelector('.chart-container');
        const chartLoading = document.getElementById('chartLoading');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Show loading indicator
        if (chartLoading) chartLoading.style.display = 'block';
        if (chartContainer) chartContainer.style.display = 'none';
        
        // Update chart title
        updateChartTitle(range);
        
        // Fetch chart data via AJAX
        fetch(`/dashboard/get-chart-data?range=${range}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                updateChart(result.data);
            } else {
                console.error('Error loading chart data:', result.message || 'Unknown error');
                showToast('error', 'Failed to load chart data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error loading chart data');
        })
        .finally(() => {
            // Hide loading indicator
            if (chartLoading) chartLoading.style.display = 'none';
            if (chartContainer) chartContainer.style.display = 'block';
        });
    }

    function updateChartTitle(range) {
        const titleMap = {
            '24h': 'Sensor Trends (Last 24 Hours)',
            '48h': 'Sensor Trends (Last 48 Hours)',
            '72h': 'Sensor Trends (Last 72 Hours)',
            '7d': 'Sensor Trends (Last 7 Days)',
            '30d': 'Sensor Trends (Last 30 Days)'
        };
        
        const chartTitle = document.getElementById('chartTitle');
        if (chartTitle) {
            chartTitle.textContent = titleMap[range] || 'Sensor Trends';
        }
    }

    function updateChart(chartData) {
        const canvas = document.getElementById('sensorChart');
        if (!canvas) {
            console.error('Chart canvas not found');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Could not get canvas context');
            return;
        }
        
        // Destroy existing chart if it exists and has destroy method
        if (window.sensorChart && typeof window.sensorChart.destroy === 'function') {
            try {
                window.sensorChart.destroy();
            } catch (e) {
                console.warn('Error destroying chart:', e);
            }
            window.sensorChart = null;
        }
        
        // Create new chart with fetched data
        window.sensorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [
                    {
                        label: 'Temperature (°C)',
                        data: chartData.temperature || [],
                        borderColor: '#4eac9b',
                        backgroundColor: 'rgba(78, 172, 155, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'pH Level',
                        data: chartData.ph || [],
                        borderColor: '#2d8f7f',
                        backgroundColor: 'rgba(45, 143, 127, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Turbidity (NTU)',
                        data: chartData.turbidity || [],
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
                            maxTicksLimit: 15
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
                            text: 'Temperature (°C) / Turbidity (NTU)',
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

<?= $this->endSection() ?>