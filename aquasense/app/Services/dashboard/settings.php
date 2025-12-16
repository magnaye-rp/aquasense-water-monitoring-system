<?= $this->include('layout/header') ?>

<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-sliders-h me-2 text-accent"></i>
                <span>System Settings</span>
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
                <i class="fas fa-info-circle me-2 text-accent"></i>
                <span>Recommended Settings</span>
            </div>
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Freshwater Aquariums:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-accent me-2"></i>
                        <strong>pH:</strong> 6.5 - 7.5
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-accent me-2"></i>
                        <strong>Temperature:</strong> 22°C - 26°C
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-accent me-2"></i>
                        <strong>Turbidity:</strong> Below 10 NTU
                    </li>
                </ul>

                <h6 class="fw-semibold mb-3 mt-4">Saltwater Aquariums:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-accent me-2"></i>
                        <strong>pH:</strong> 8.0 - 8.4
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-accent me-2"></i>
                        <strong>Temperature:</strong> 24°C - 28°C
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-accent me-2"></i>
                        <strong>Turbidity:</strong> Below 5 NTU
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$custom_scripts = "
// Handle form submission
$('#settingsForm').submit(function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = $(this).serialize();
    
    // Show loading state
    const submitBtn = $(this).find('button[type=\"submit\"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class=\"fas fa-spinner fa-spin me-2\"></i> Saving...');
    submitBtn.prop('disabled', true);
    
    // Send AJAX request
    $.ajax({
        url: '" . base_url('dashboard/update-settings') . "',
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
                showAlert('error', errorMessage);
            }
        },
        error: function() {
            showAlert('error', 'Network error. Please try again.');
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
        <strong>\${type === 'success' ? 'Success!' : 'Error!'}</strong> \${message}
        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
";
?>

<?= $this->include('layout/footer') ?>