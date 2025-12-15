<?= $this->include('layout/header') ?>

<div class="row mb-4">
    <!-- Device Control -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-cogs me-2 text-accent"></i>
                <span>Device Control</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Oxygenator -->
                    <div class="col-md-6 mb-3">
                        <div class="card device-card h-100 <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'device-on' : 'device-off' ?>">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-wind device-icon"></i>
                                <h5 class="card-title mb-3">Oxygenator</h5>
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="oxySwitch" <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'checked' : '' ?>
                                               onchange="controlDevice('oxygenator', this.checked ? 'on' : 'off')">
                                        <label class="form-check-label ms-2 fw-medium" for="oxySwitch">
                                            <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                        </label>
                                    </div>
                                </div>
                                <?php if ($currentStatus['oxygenator']['last_updated']): ?>
                                    <small class="text-muted">
                                        Last updated: <?= date('H:i', strtotime($currentStatus['oxygenator']['last_updated'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Water Pump -->
                    <div class="col-md-6 mb-3">
                        <div class="card device-card h-100 <?= $currentStatus['water_pump']['state'] == 'ON' ? 'device-on' : 'device-off' ?>">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-tint device-icon"></i>
                                <h5 class="card-title mb-3">Water Pump</h5>
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="pumpSwitch" <?= $currentStatus['water_pump']['state'] == 'ON' ? 'checked' : '' ?>
                                               onchange="controlDevice('water_pump', this.checked ? 'on' : 'off')">
                                        <label class="form-check-label ms-2 fw-medium" for="pumpSwitch">
                                            <?= $currentStatus['water_pump']['state'] == 'ON' ? 'ON' : 'OFF' ?>
                                        </label>
                                    </div>
                                </div>
                                <?php if ($currentStatus['water_pump']['last_updated']): ?>
                                    <small class="text-muted">
                                        Last updated: <?= date('H:i', strtotime($currentStatus['water_pump']['last_updated'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Device History -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-history me-2 text-accent"></i>
                <span>Device History</span>
            </div>
            <div class="card-body">
                <?php if (empty($deviceHistory)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x mb-3 text-accent opacity-50"></i>
                        <p class="text-muted mb-0">No device history available</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Device</th>
                                    <th>Action</th>
                                    <th>Triggered By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deviceHistory as $log): ?>
                                    <tr>
                                        <td><?= date('H:i', strtotime($log['created_at'])) ?></td>
                                        <td><?= ucfirst(str_replace('_', ' ', $log['device_name'])) ?></td>
                                        <td>
                                            <span class="badge <?= $log['action'] == 'ON' ? 'status-badge' : 'bg-secondary' ?>">
                                                <?= $log['action'] ?>
                                            </span>
                                        </td>
                                        <td><?= ucfirst($log['triggered_by']) ?></td>
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

<?php
$custom_scripts = "
// Device control function
function controlDevice(device, action) {
    console.log('Attempting to control: ' + device + ' -> ' + action);
    
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
            } else {
                showAlert('error', 'Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showAlert('error', 'Network error. Check console for details.');
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
        switchElement.nextElementSibling.textContent = action.toUpperCase();
    }
    
    const card = document.querySelector('.device-card:contains(' + (device === 'oxygenator' ? 'Oxygenator' : 'Water Pump') + ')');
    if (card) {
        card.classList.remove('device-on', 'device-off');
        card.classList.add(action === 'on' ? 'device-on' : 'device-off');
    }
}
";
?>

<?= $this->include('layout/footer') ?>