
<?= $this->extend('layout/header') ?>

<?= $this->section('content') ?>
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

    <div class="row mb-4">
        <!-- Auto Mode Control -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-robot me-2" style="color: #ffd700;"></i>
                    <span style="color: white; font-weight: 600;">Auto Mode Control</span>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex justify-content-between align-items-center p-4 border border-secondary rounded" style="background: rgba(78, 172, 155, 0.1);">
                                <div>
                                    <h5 class="mb-1 fw-bold" style="color: #66CFC4;">Auto Mode</h5>
                                    <small style="color: #b0c4be;">Automatically control all devices based on sensor readings</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="autoModeSwitch" <?= (isset($settings) && (($settings['oxygenator_auto'] ?? 0) || ($settings['pump_auto'] ?? 0))) ? 'checked' : '' ?>
                                           data-type="auto-mode">
                                    <label class="form-check-label ms-2 fw-medium" for="autoModeSwitch" id="autoModeLabel" style="color: white;">
                                        <?= (isset($settings) && (($settings['oxygenator_auto'] ?? 0) || ($settings['pump_auto'] ?? 0))) ? 'ON' : 'OFF' ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Control -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-cogs me-2" style="color: #ffd700;"></i>
                    <span style="color: white; font-weight: 600;">Device Control</span>
                </div>
                <div class="card-body">
                    <?php 
                    $autoModeEnabled = isset($settings) && (($settings['oxygenator_auto'] ?? 0) || ($settings['pump_auto'] ?? 0));
                    if ($autoModeEnabled): 
                    ?>
                    <div class="alert alert-info mb-3" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Auto Mode Active:</strong> Manual controls will work, but may be overridden by automatic mode based on sensor readings.
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <!-- Oxygenator -->
                        <div class="col-md-6 mb-3">
                            <div class="card device-card h-100 <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'device-on' : 'device-off' ?>" id="oxyCard">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-wind device-icon"></i>
                                    <h5 class="card-title mb-3">Oxygenator</h5>
                                    <?php if (isset($settings) && ($settings['oxygenator_auto'] ?? 0)): ?>
                                        <span class="badge bg-info mb-2">
                                            <i class="fas fa-robot me-1"></i>Auto Mode
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Toggle Switch for Oxygenator -->
                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="oxygenatorSwitch" 
                                                   data-device="oxygenator"
                                                   <?= $currentStatus['oxygenator']['state'] == 'ON' ? 'checked' : '' ?>
                                                   style="width: 3em; height: 1.5em;">
                                            <label class="form-check-label ms-2 fw-medium" for="oxygenatorSwitch" id="oxygenatorLabel" style="color: white;">
                                                <?= $currentStatus['oxygenator']['state'] ?>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <?php if ($currentStatus['oxygenator']['last_updated']): ?>
                                        <small class="d-block">
                                            Last updated: <?= date('H:i', strtotime($currentStatus['oxygenator']['last_updated'])) ?>
                                        </small>
                                    <?php endif; ?>
                                    <?php if ($currentStatus['oxygenator']['triggered_by']): ?>
                                        <small class="d-block text-muted">
                                            Triggered by: <?= ucfirst($currentStatus['oxygenator']['triggered_by']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Water Pump -->
                        <div class="col-md-6 mb-3">
                            <div class="card device-card h-100 <?= $currentStatus['water_pump']['state'] == 'ON' ? 'device-on' : 'device-off' ?>" id="pumpCard">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-tint device-icon"></i>
                                    <h5 class="card-title mb-3">Water Pump</h5>
                                    <?php if (isset($settings) && ($settings['pump_auto'] ?? 0)): ?>
                                        <span class="badge bg-info mb-2">
                                            <i class="fas fa-robot me-1"></i>Auto Mode
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Toggle Switch for Water Pump -->
                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="waterPumpSwitch" 
                                                   data-device="water_pump"
                                                   <?= $currentStatus['water_pump']['state'] == 'ON' ? 'checked' : '' ?>
                                                   style="width: 3em; height: 1.5em;">
                                            <label class="form-check-label ms-2 fw-medium" for="waterPumpSwitch" id="waterPumpLabel" style="color: white;">
                                                <?= $currentStatus['water_pump']['state'] ?>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <?php if ($currentStatus['water_pump']['last_updated']): ?>
                                        <small class="d-block">
                                            Last updated: <?= date('H:i', strtotime($currentStatus['water_pump']['last_updated'])) ?>
                                        </small>
                                    <?php endif; ?>
                                    <?php if ($currentStatus['water_pump']['triggered_by']): ?>
                                        <small class="d-block text-muted">
                                            Triggered by: <?= ucfirst($currentStatus['water_pump']['triggered_by']) ?>
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
                    <i class="fas fa-history me-2" style="color: #ffd700;"></i>
                    <span style="color: white; font-weight: 600;">Device History</span>
                </div>
                <div class="card-body">
                    <?php if (empty($deviceHistory)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x mb-3" style="color: #4eac9b; opacity: 0.5;"></i>
                            <p style="color: #b0c4be;">No device history available</p>
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
                        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                            <nav aria-label="Page navigation" class="mt-3">
                                <?= $pager->links() ?>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    console.log('Dashboard JS loaded');

    // Initialize device controls after DOM is loaded
    $(document).ready(function() {
        console.log('jQuery loaded and ready');
        
        // Handle device toggle switch changes
        $(document).on('change', '.form-check-input[data-device]', function() {
            const device = $(this).data('device');
            const isChecked = $(this).is(':checked');
            const newState = isChecked ? 'on' : 'off';
            const switchElement = $(this);
            const labelMap = {
                oxygenator: '#oxygenatorLabel',
                water_pump: '#waterPumpLabel'
            };
            const label = $(labelMap[device]);

            
            console.log('Device control triggered:', device, '→', newState);
            
            // Disable switch temporarily
            switchElement.prop('disabled', true);
            
            // Store original state for rollback
            const originalState = !isChecked;
            
            // Use csrf_token() function
            const csrfName = '<?= csrf_token() ?>';
            const csrfHash = '<?= csrf_hash() ?>';
            const url = '<?= base_url('dashboard/control-device') ?>';

            console.log('URL:', url);
            
            // Show loading state
            label.html('<span class="spinner-border spinner-border-sm me-1"></span>Processing');
            
            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    device: device,
                    action: newState,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response:', response);
                    
                    if (response.success) {
                        // Success - update label
                        label.text(newState.toUpperCase());
                        
                        // Update card styling
                        updateDeviceCard(device, newState);
                        
                        // Show success message
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message);
                        } else {
                            console.log('Success:', response.message);
                        }
                        
                        // Refresh device history if needed
                        setTimeout(() => {
                            // You can optionally reload the history section
                            // location.reload(); // Simple refresh for testing
                        }, 1000);
                        
                    } else {
                        // Error - revert switch
                        console.error('Server error:', response.message);
                        switchElement.prop('checked', originalState);
                        label.text(originalState ? 'ON' : 'OFF');
                        
                        if (typeof window.showToast === 'function') {
                            window.showToast('error', response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('XHR:', xhr);
                    
                    // Revert switch on error
                    switchElement.prop('checked', originalState);
                    label.text(originalState ? 'ON' : 'OFF');
                    
                    let errorMsg = 'Network error. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        errorMsg = 'Server error: ' + xhr.responseText;
                    }
                    
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', errorMsg);
                    } else {
                        alert('Error: ' + errorMsg);
                    }
                },
                complete: function() {
                    // Re-enable switch
                    switchElement.prop('disabled', false);
                }
            });
        });
        
        // Handle auto mode switch changes - toggle both devices
        $(document).on('change', '#autoModeSwitch', function() {
            const enabled = $(this).is(':checked') ? 1 : 0;
            const switchElement = $(this);
            
            console.log('Auto mode toggle triggered:', enabled);
            
            // Disable switch temporarily
            switchElement.prop('disabled', true);
            
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const url = '<?= base_url('dashboard/toggle-auto-mode') ?>';

            const csrfName = '<?= csrf_token() ?>';
            const csrfHash = '<?= csrf_hash() ?>';
            
            // Toggle both devices' auto mode
            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    enabled: enabled,
                    toggle_all: true,  // Flag to toggle both devices
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Server response:', response);
                    
                    if (response.success) {
                        // Update label text
                        $('#autoModeLabel').text(enabled ? 'ON' : 'OFF');
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message);
                        } else {
                            alert('Auto mode ' + (enabled ? 'enabled' : 'disabled') + ' for all devices');
                        }
                    } else {
                        const errorMsg = 'Error: ' + response.message;
                        if (typeof window.showToast === 'function') {
                            window.showToast('error', errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                        // Revert switch state
                        switchElement.prop('checked', !switchElement.prop('checked'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error, xhr);
                    let errorMsg = 'Network error. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                    // Revert switch state
                    switchElement.prop('checked', !switchElement.prop('checked'));
                },
                complete: function() {
                    switchElement.prop('disabled', false);
                }
            });
        });
    });
    
    // Update device card styling
    function updateDeviceCard(device, action) {
        const cardId = device === 'oxygenator' ? '#oxyCard' : '#pumpCard';
        const card = $(cardId);
        
        if (card.length) {
            card.removeClass('device-on device-off');
            card.addClass(action === 'on' ? 'device-on' : 'device-off');
            
            // Update icon color
            const icon = card.find('.device-icon');
            if (icon.length) {
                icon.css('color', action === 'on' ? '#4eac9b' : '#666');
                
                // Add glow effect
                if (action === 'on') {
                    icon.css('filter', 'drop-shadow(0 0 10px rgba(78, 172, 155, 0.5))');
                } else {
                    icon.css('filter', 'none');
                }
            }
        }
    }
</script>
<style>
    /* Toggle Switch Styling */
    .form-check-input[type="checkbox"] {
        width: 3em !important;
        height: 1.5em !important;
        border-radius: 1.5em !important;
        background-color: rgba(102, 102, 102, 0.5) !important;
        border: 2px solid #666 !important;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }
    
    .form-check-input[type="checkbox"]:checked {
        background-color: #4eac9b !important;
        border-color: #4eac9b !important;
    }
    
    .form-check-input[type="checkbox"]:focus {
        box-shadow: 0 0 0 0.25rem rgba(78, 172, 155, 0.25) !important;
        border-color: #4eac9b !important;
    }
    
    .form-check-input[type="checkbox"]:hover {
        border-color: #4eac9b !important;
    }
    
    /* Switch toggle circle */
    .form-check-input[type="checkbox"]::before {
        content: '';
        position: absolute;
        width: 1.2em;
        height: 1.2em;
        border-radius: 50%;
        background-color: white;
        top: 50%;
        left: 0.15em;
        transform: translateY(-50%);
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .form-check-input[type="checkbox"]:checked::before {
        left: calc(100% - 1.35em);
        background-color: white;
    }
    
    .form-check-input[type="checkbox"]:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .form-switch .form-check-input {
        width: 3em !important;
        height: 1.5em !important;
        margin-top: 0.125em;
        vertical-align: top;
        background-image: none !important;
        background-position: left center;
        border-radius: 1.5em !important;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    
    .form-switch .form-check-input:checked {
        background-position: right center;
        background-image: none !important;
    }
    
    .form-check-label {
        color: white;
        cursor: pointer;
        margin-left: 10px;
        font-weight: 500;
        min-width: 40px;
    }
    
    /* Device Cards */
    .device-card {
        background: linear-gradient(135deg, rgba(78, 172, 155, 0.15) 0%, rgba(45, 90, 90, 0.2) 100%) !important;
        border: 2px solid #666 !important;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    
    .device-card.device-on {
        border-color: #4eac9b !important;
        box-shadow: 0 0 20px rgba(78, 172, 155, 0.3);
    }
    
    .device-card.device-off {
        border-color: #666 !important;
        opacity: 0.8;
    }
    
    .device-icon {
        font-size: 3rem;
        color: #666;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }
    
    .device-card.device-on .device-icon {
        color: #4eac9b;
        filter: drop-shadow(0 0 10px rgba(78, 172, 155, 0.5));
    }
    
    .device-card .card-title {
        color: white;
        font-weight: 600;
    }
    
    .device-card small {
        color: #b0c4be;
    }
    
    /* Status badge */
    .status-badge {
        background: linear-gradient(135deg, #4eac9b 0%, #2d8f7f 100%) !important;
        color: white !important;
    }
</style>
<?= $this->endSection() ?>