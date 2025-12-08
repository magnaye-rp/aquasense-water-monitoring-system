<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
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
        }
        .device-on {
            border-left: 5px solid #198754;
            background-color: rgba(25, 135, 84, 0.05);
        }
        .device-off {
            border-left: 5px solid #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        .control-btn {
            min-width: 120px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .control-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .table th {
            border-top: none;
            font-weight: 600;
        }
        .auto-badge {
            background-color: #198754;
        }
        .manual-badge {
            background-color: #0dcaf0;
        }
        .alert-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <!-- Current Device Status -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-plug me-2"></i> Device Control
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Oxygenator -->
                            <div class="col-md-6 mb-4">
                                <div class="card device-card h-100 <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'device-on' : 'device-off' ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-wind fa-2x <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'text-success' : 'text-secondary' ?>"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-1">Oxygenator</h5>
                                                <p class="card-text text-muted mb-0">
                                                    <small>
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?= $currentStatus['oxygenator']['last_updated'] ? 
                                                            date('M d, H:i', strtotime($currentStatus['oxygenator']['last_updated'])) : 
                                                            'Never updated' ?>
                                                    </small>
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'bg-success' : 'bg-danger' ?> status-badge">
                                                    <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-muted">
                                                Mode: 
                                                <span class="badge <?= $settings['oxygenator_auto'] == 1 ? 'auto-badge' : 'manual-badge' ?>">
                                                    <?= $settings['oxygenator_auto'] == 1 ? 'Auto' : 'Manual' ?>
                                                </span>
                                            </small>
                                        </div>
                                        
                                        <!-- Single Toggle Button -->
                                        <div class="d-grid">
                                            <?php if ($currentStatus['oxygenator']['state'] == 'ON'): ?>
                                                <button class="btn btn-danger control-btn" 
                                                        onclick="controlDevice('oxygenator', 'off')"
                                                        id="oxyBtn">
                                                    <i class="fas fa-power-off me-2"></i> Turn OFF
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-success control-btn" 
                                                        onclick="controlDevice('oxygenator', 'on')"
                                                        id="oxyBtn">
                                                    <i class="fas fa-power-off me-2"></i> Turn ON
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Water Pump -->
                            <div class="col-md-6 mb-4">
                                <div class="card device-card h-100 <?= $currentStatus['water_pump']['state'] == 'ON' ? 'device-on' : 'device-off' ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <i class="fas fa-tint fa-2x <?= $currentStatus['water_pump']['state'] == 'ON' ? 'text-success' : 'text-secondary' ?>"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-1">Water Pump</h5>
                                                <p class="card-text text-muted mb-0">
                                                    <small>
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?= $currentStatus['water_pump']['last_updated'] ? 
                                                            date('M d, H:i', strtotime($currentStatus['water_pump']['last_updated'])) : 
                                                            'Never updated' ?>
                                                    </small>
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge <?= $currentStatus['water_pump']['state'] == 'ON' ? 'bg-success' : 'bg-danger' ?> status-badge">
                                                    <?= $currentStatus['water_pump']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-muted">
                                                Mode: 
                                                <span class="badge <?= $settings['pump_auto'] == 1 ? 'auto-badge' : 'manual-badge' ?>">
                                                    <?= $settings['pump_auto'] == 1 ? 'Auto' : 'Manual' ?>
                                                </span>
                                            </small>
                                        </div>
                                        
                                        <!-- Single Toggle Button -->
                                        <div class="d-grid">
                                            <?php if ($currentStatus['water_pump']['state'] == 'ON'): ?>
                                                <button class="btn btn-danger control-btn" 
                                                        onclick="controlDevice('water_pump', 'off')"
                                                        id="pumpBtn">
                                                    <i class="fas fa-power-off me-2"></i> Turn OFF
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-success control-btn" 
                                                        onclick="controlDevice('water_pump', 'on')"
                                                        id="pumpBtn">
                                                    <i class="fas fa-power-off me-2"></i> Turn ON
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-outline-primary me-2" onclick="controlBoth('on')">
                                        <i class="fas fa-play me-2"></i> Turn Both ON
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="controlBoth('off')">
                                        <i class="fas fa-stop me-2"></i> Turn Both OFF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <i class="fas fa-history me-2"></i> Recent Activity
                    </div>
                    <div class="card-body">
                        <?php if (empty($deviceHistory)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-history fa-2x mb-3"></i>
                                <p class="mb-0">No recent activity</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Device</th>
                                            <th>Action</th>
                                            <th>By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($deviceHistory as $log): ?>
                                            <tr>
                                                <td><?= date('H:i:s', strtotime($log['created_at'])) ?></td>
                                                <td>
                                                    <?= ucfirst(str_replace('_', ' ', $log['device_name'])) ?>
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
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Device control functions - SIMPLIFIED
        async function controlDevice(device, action) {
            const button = document.getElementById(device === 'oxygenator' ? 'oxyBtn' : 'pumpBtn');
            const originalText = button.innerHTML;
            
            console.log(`Controlling ${device} to ${action}`);
            
            // Show loading
            button.innerHTML = '<span class="loading-spinner me-2"></span> Processing...';
            button.disabled = true;
            
            try {
                const response = await fetch('<?= base_url("dashboard/control-device") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        device: device,
                        action: action
                    })
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    showAlert('success', data.message);
                    // Update UI immediately
                    updateButtonState(device, action === 'on');
                    
                    // Also refresh the page after 2 seconds to sync with Arduino
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    throw new Error(data.message || 'Server returned error');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', error.message);
                
                // Restore button
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }

        function updateButtonState(device, isOn) {
            const button = document.getElementById(device === 'oxygenator' ? 'oxyBtn' : 'pumpBtn');
            const card = button.closest('.device-card');
            const badge = card.querySelector('.status-badge');
            const icon = card.querySelector('.fa-wind, .fa-tint');
            
            if (isOn) {
                // Change to OFF button
                button.innerHTML = '<i class="fas fa-power-off me-2"></i> Turn OFF';
                button.className = 'btn btn-danger control-btn';
                button.onclick = () => controlDevice(device, 'off');
                
                // Update card style
                card.classList.remove('device-off');
                card.classList.add('device-on');
                
                // Update status badge
                badge.className = 'badge bg-success status-badge';
                badge.textContent = 'ON';
                
                // Update icon
                if (icon) icon.classList.remove('text-secondary');
                if (icon) icon.classList.add('text-success');
                
            } else {
                // Change to ON button
                button.innerHTML = '<i class="fas fa-power-off me-2"></i> Turn ON';
                button.className = 'btn btn-success control-btn';
                button.onclick = () => controlDevice(device, 'on');
                
                // Update card style
                card.classList.remove('device-on');
                card.classList.add('device-off');
                
                // Update status badge
                badge.className = 'badge bg-danger status-badge';
                badge.textContent = 'OFF';
                
                // Update icon
                if (icon) icon.classList.remove('text-success');
                if (icon) icon.classList.add('text-secondary');
            }
            
            button.disabled = false;
        }
        function controlBoth(action) {
            showAlert('info', `Turning both devices ${action.toUpperCase()}...`);

            controlDevice('oxygenator', action);
            setTimeout(() => {
                controlDevice('water_pump', action);
            }, 500);
        }

        function showAlert(type, message) {
            // Remove existing alerts
            const existing = document.querySelector('.alert-fixed');
            if (existing) existing.remove();
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-fixed`;
            alertDiv.innerHTML = `
                <strong>${type === 'success' ? '✓' : '✗'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        }
    </script>
</body>
</html>