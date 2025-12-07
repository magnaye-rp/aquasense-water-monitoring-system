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
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
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
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
        .setting-card {
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        .setting-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .water-type-card {
            cursor: pointer;
            transition: all 0.3s;
        }
        .water-type-card.selected {
            border: 2px solid #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }
        .water-type-card:hover {
            transform: scale(1.02);
        }
        .range-slider {
            width: 100%;
            height: 6px;
            border-radius: 5px;
            background: #ddd;
            outline: none;
            -webkit-appearance: none;
        }
        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #0d6efd;
            cursor: pointer;
        }
        .range-value {
            font-weight: 600;
            color: #0d6efd;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .save-btn {
            border-radius: 20px;
            padding: 10px 30px;
            font-weight: 600;
        }
        .info-tooltip {
            cursor: help;
            color: #6c757d;
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
                        <a class="nav-link" href="<?= base_url('dashboard/devices') ?>">
                            <i class="fas fa-cogs me-1"></i> Device Control
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('dashboard/settings') ?>">
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
        <!-- Settings Form -->
        <div class="row">
            <div class="col-lg-8">
                <form id="settingsForm" method="POST" action="<?= base_url('dashboard/update-settings') ?>">
                    <?= csrf_field() ?>
                    
                    <!-- Water Type Selection -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-water me-2"></i> Water Type Configuration
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($waterTypes as $key => $label): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card water-type-card h-100 <?= $settings['water_type'] == $key ? 'selected' : '' ?>"
                                             onclick="selectWaterType('<?= $key ?>')">
                                            <div class="card-body text-center">
                                                <?php if ($key == 'freshwater'): ?>
                                                    <i class="fas fa-fish fa-3x text-info mb-3"></i>
                                                    <h6><?= $label ?></h6>
                                                    <small class="text-muted">Ideal for most freshwater fish</small>
                                                    <div class="mt-2">
                                                        <small>Temp: 20-28°C</small><br>
                                                        <small>pH: 6.5-7.5</small>
                                                    </div>
                                                <?php elseif ($key == 'saltwater'): ?>
                                                    <i class="fas fa-water fa-3x text-primary mb-3"></i>
                                                    <h6><?= $label ?></h6>
                                                    <small class="text-muted">Marine aquarium settings</small>
                                                    <div class="mt-2">
                                                        <small>Temp: 24-28°C</small><br>
                                                        <small>pH: 7.8-8.4</small>
                                                    </div>
                                                <?php else: ?>
                                                    <i class="fas fa-flask fa-3x text-secondary mb-3"></i>
                                                    <h6><?= $label ?></h6>
                                                    <small class="text-muted">General water monitoring</small>
                                                    <div class="mt-2">
                                                        <small>Temp: 20-30°C</small><br>
                                                        <small>pH: 6.5-8.5</small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="water_type" id="waterType" value="<?= $settings['water_type'] ?? 'generic' ?>">
                        </div>
                    </div>

                    <!-- Sensor Thresholds -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-chart-line me-2"></i> Sensor Thresholds
                        </div>
                        <div class="card-body">
                            <!-- Temperature Range -->
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between">
                                    <span>
                                        <i class="fas fa-thermometer-half me-2 text-danger"></i>
                                        Temperature Range (°C)
                                        <i class="fas fa-info-circle info-tooltip ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Set the acceptable temperature range for your water type"></i>
                                    </span>
                                    <span class="range-value" id="tempRangeValue">
                                        <?php 
                                        if (isset($settings['temperature_range']) && !empty($settings['temperature_range'])) {
                                            $range = explode('-', $settings['temperature_range']);
                                            echo $range[0] . '°C - ' . $range[1] . '°C';
                                        } else {
                                            echo '20°C - 30°C';
                                        }
                                        ?>
                                    </span>
                                </label>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label>Minimum</label>
                                        <input type="range" class="range-slider" id="tempMin" min="15" max="25" step="0.5" 
                                               value="<?= isset($settings['temperature_range']) ? explode('-', $settings['temperature_range'])[0] : 20 ?>"
                                               oninput="updateTempRange()">
                                        <div class="text-center">
                                            <span class="badge bg-secondary" id="tempMinValue">
                                                <?= isset($settings['temperature_range']) ? explode('-', $settings['temperature_range'])[0] : 20 ?>°C
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label>Maximum</label>
                                        <input type="range" class="range-slider" id="tempMax" min="25" max="35" step="0.5" 
                                               value="<?= isset($settings['temperature_range']) ? explode('-', $settings['temperature_range'])[1] : 30 ?>"
                                               oninput="updateTempRange()">
                                        <div class="text-center">
                                            <span class="badge bg-secondary" id="tempMaxValue">
                                                <?= isset($settings['temperature_range']) ? explode('-', $settings['temperature_range'])[1] : 30 ?>°C
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="temperature_range" id="temperatureRange" 
                                       value="<?= $settings['temperature_range'] ?? '20-30' ?>">
                            </div>

                            <!-- pH Range -->
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between">
                                    <span>
                                        <i class="fas fa-vial me-2 text-info"></i>
                                        pH Level Range
                                        <i class="fas fa-info-circle info-tooltip ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Set the acceptable pH range for your water type"></i>
                                    </span>
                                    <span class="range-value" id="phRangeValue">
                                        <?= $settings['ph_good_min'] ?? 6.5 ?> - <?= $settings['ph_good_max'] ?? 8.5 ?>
                                    </span>
                                </label>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label>Minimum pH</label>
                                        <input type="number" class="form-control" name="ph_good_min" 
                                               min="4" max="9" step="0.1"
                                               value="<?= $settings['ph_good_min'] ?? 6.5 ?>"
                                               oninput="updatePhRange()">
                                    </div>
                                    <div class="col-6">
                                        <label>Maximum pH</label>
                                        <input type="number" class="form-control" name="ph_good_max" 
                                               min="5" max="10" step="0.1"
                                               value="<?= $settings['ph_good_max'] ?? 8.5 ?>"
                                               oninput="updatePhRange()">
                                    </div>
                                </div>
                            </div>

                            <!-- Turbidity Limit -->
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between">
                                    <span>
                                        <i class="fas fa-water me-2 text-warning"></i>
                                        Turbidity Limit (NTU)
                                        <i class="fas fa-info-circle info-tooltip ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Maximum turbidity level before triggering alerts"></i>
                                    </span>
                                    <span class="range-value" id="turbidityValue">
                                        <?= $settings['turbidity_limit'] ?? 100 ?> NTU
                                    </span>
                                </label>
                                <input type="range" class="range-slider mb-2" id="turbidityLimit" 
                                       name="turbidity_limit" min="10" max="500" step="10"
                                       value="<?= $settings['turbidity_limit'] ?? 100 ?>"
                                       oninput="updateTurbidityValue()">
                                <div class="d-flex justify-content-between">
                                    <small>Clear (10 NTU)</small>
                                    <small>Very Turbid (500 NTU)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Device Control Settings -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-cogs me-2"></i> Device Control Settings
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Oxygenator Settings -->
                                <div class="col-md-6 mb-4">
                                    <div class="card setting-card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-wind me-2 text-info"></i>
                                                Oxygenator Settings
                                            </h6>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="oxygenatorAuto" name="oxygenator_auto" value="1"
                                                       <?= ($settings['oxygenator_auto'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="oxygenatorAuto">
                                                    Automatic Mode
                                                </label>
                                                <small class="text-muted d-block">Automatically control based on sensor readings</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Operation Interval (minutes)</label>
                                                <input type="number" class="form-control" 
                                                       name="oxygenator_interval" min="1" max="240"
                                                       value="<?= $settings['oxygenator_interval'] ?? 60 ?>">
                                                <small class="text-muted">How often to run in auto mode (1-240 minutes)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Water Pump Settings -->
                                <div class="col-md-6 mb-4">
                                    <div class="card setting-card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-tint me-2 text-primary"></i>
                                                Water Pump Settings
                                            </h6>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="pumpAuto" name="pump_auto" value="1"
                                                       <?= ($settings['pump_auto'] ?? 0) == 1 ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="pumpAuto">
                                                    Automatic Mode
                                                </label>
                                                <small class="text-muted">Automatically control based on sensor readings</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Operation Interval (minutes)</label>
                                                <input type="number" class="form-control" 
                                                       name="pump_interval" min="1" max="240"
                                                       value="<?= $settings['pump_interval'] ?? 60 ?>">
                                                <small class="text-muted">How often to run in auto mode (1-240 minutes)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="text-center mb-4">
                        <button type="submit" class="btn btn-primary save-btn">
                            <i class="fas fa-save me-2"></i> Save All Settings
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetToDefaults()">
                            <i class="fas fa-undo me-2"></i> Reset to Defaults
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar - Current Settings Summary -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-info-circle me-2"></i> Current Settings Summary
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Water Type</h6>
                            <span class="badge bg-primary">
                                <?= ucfirst($settings['water_type'] ?? 'generic') ?>
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Temperature Range</h6>
                            <div class="progress" style="height: 20px;">
                                <?php
                                $tempRange = isset($settings['temperature_range']) ? explode('-', $settings['temperature_range']) : [20, 30];
                                $minTemp = $tempRange[0];
                                $maxTemp = $tempRange[1];
                                $position = (($minTemp - 15) / 20) * 100;
                                $width = (($maxTemp - $minTemp) / 20) * 100;
                                ?>
                                <div class="progress-bar bg-danger" 
                                     style="width: <?= $width ?>%; margin-left: <?= $position ?>%;">
                                    <?= $minTemp ?>°C - <?= $maxTemp ?>°C
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6>pH Range</h6>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2"><?= $settings['ph_good_min'] ?? 6.5 ?></span>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-info" style="width: 100%;"></div>
                                    </div>
                                </div>
                                <span class="badge bg-info ms-2"><?= $settings['ph_good_max'] ?? 8.5 ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Turbidity Limit</h6>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-water text-warning me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 10px;">
                                        <?php
                                        $turbidity = $settings['turbidity_limit'] ?? 100;
                                        $turbidityPercent = ($turbidity / 500) * 100;
                                        ?>
                                        <div class="progress-bar bg-warning" style="width: <?= $turbidityPercent ?>%;">
                                            <?= $turbidity ?> NTU
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Device Modes</h6>
                            <div class="d-flex">
                                <div class="me-3">
                                    <small>Oxygenator:</small><br>
                                    <span class="badge <?= ($settings['oxygenator_auto'] ?? 0) == 1 ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ($settings['oxygenator_auto'] ?? 0) == 1 ? 'Auto' : 'Manual' ?>
                                    </span>
                                </div>
                                <div>
                                    <small>Water Pump:</small><br>
                                    <span class="badge <?= ($settings['pump_auto'] ?? 0) == 1 ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ($settings['pump_auto'] ?? 0) == 1 ? 'Auto' : 'Manual' ?>
                                    </span>
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
        // Initialize tooltips
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Water type selection
        function selectWaterType(type) {
            // Update hidden input
            $('#waterType').val(type);
            
            // Update card selection
            $('.water-type-card').removeClass('selected');
            $(`.water-type-card:contains('${type.charAt(0).toUpperCase() + type.slice(1)}')`).closest('.water-type-card').addClass('selected');
            
            // Update thresholds based on water type
            updateThresholdsForWaterType(type);
        }

        // Update thresholds based on water type
        function updateThresholdsForWaterType(type) {
            let tempMin, tempMax, phMin, phMax, turbidity;
            
            switch(type) {
                case 'freshwater':
                    tempMin = 20;
                    tempMax = 28;
                    phMin = 6.5;
                    phMax = 7.5;
                    turbidity = 50;
                    break;
                case 'saltwater':
                    tempMin = 24;
                    tempMax = 28;
                    phMin = 7.8;
                    phMax = 8.4;
                    turbidity = 30;
                    break;
                case 'generic':
                default:
                    tempMin = 20;
                    tempMax = 30;
                    phMin = 6.5;
                    phMax = 8.5;
                    turbidity = 100;
                    break;
            }
            
            // Update sliders and inputs
            $('#tempMin').val(tempMin);
            $('#tempMax').val(tempMax);
            $('input[name="ph_good_min"]').val(phMin);
            $('input[name="ph_good_max"]').val(phMax);
            $('#turbidityLimit').val(turbidity);
            
            // Update displayed values
            updateTempRange();
            updatePhRange();
            updateTurbidityValue();
        }

        // Update temperature range display
        function updateTempRange() {
            const min = $('#tempMin').val();
            const max = $('#tempMax').val();
            
            $('#tempMinValue').text(min + '°C');
            $('#tempMaxValue').text(max + '°C');
            $('#tempRangeValue').text(min + '°C - ' + max + '°C');
            $('#temperatureRange').val(min + '-' + max);
        }

        // Update pH range display
        function updatePhRange() {
            const min = $('input[name="ph_good_min"]').val();
            const max = $('input[name="ph_good_max"]').val();
            $('#phRangeValue').text(min + ' - ' + max);
        }

        // Update turbidity value display
        function updateTurbidityValue() {
            const value = $('#turbidityLimit').val();
            $('#turbidityValue').text(value + ' NTU');
        }

        // Reset to defaults
        function resetToDefaults() {
            if (confirm('Are you sure you want to reset all settings to defaults? This cannot be undone.')) {
                selectWaterType('generic');
                $('#oxygenatorAuto').prop('checked', false);
                $('#pumpAuto').prop('checked', false);
                $('input[name="oxygenator_interval"]').val(60);
                $('input[name="pump_interval"]').val(60);
                alert('Settings reset to defaults. Click "Save All Settings" to apply.');
            }
        }

        // Form submission
        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validate pH range
            const phMin = parseFloat($('input[name="ph_good_min"]').val());
            const phMax = parseFloat($('input[name="ph_good_max"]').val());
            
            if (phMin >= phMax) {
                alert('pH Minimum must be less than pH Maximum');
                return;
            }
            
            // Validate temperature range
            const tempMin = parseFloat($('#tempMin').val());
            const tempMax = parseFloat($('#tempMax').val());
            
            if (tempMin >= tempMax) {
                alert('Temperature Minimum must be less than Temperature Maximum');
                return;
            }
            
            // Submit form via AJAX
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        alert('Settings saved successfully!');
                        window.location.reload();
                    } else {
                        if (response.errors) {
                            let errorMsg = 'Please fix the following errors:\n';
                            for (const error in response.errors) {
                                errorMsg += `• ${response.errors[error]}\n`;
                            }
                            alert(errorMsg);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                },
                error: function() {
                    alert('Error saving settings. Please try again.');
                }
            });
        });

        // Initialize on page load
        $(document).ready(function() {
            updateTempRange();
            updatePhRange();
            updateTurbidityValue();
        });
    </script>
</body>
</html>