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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-sliders-h me-2" style="color: #ffd700;"></i>
                    <span style="color: white; font-weight: 600;">System Settings</span>
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

                        <!-- Temperature Range Dropdown -->
                        <div class="mb-4">
                            <label for="temp_range" class="form-label fw-semibold">Temperature Range (°C)</label>
                            <select class="form-select" id="temp_range" name="temperature_range">
                                <?php
                                $tempRanges = [
                                    '15-20' => '15°C - 20°C (Cold water fish)',
                                    '18-22' => '18°C - 22°C (Cool water fish)',
                                    '20-24' => '20°C - 24°C (Tropical fish)',
                                    '22-26' => '22°C - 26°C (Most freshwater)',
                                    '24-28' => '24°C - 28°C (Tropical community)',
                                    '26-30' => '26°C - 30°C (Discus/Rams)',
                                    '28-32' => '28°C - 32°C (Marine/coral)'
                                ];
                                
                                $currentRange = $settings['temperature_range'] ?? '22-26';
                                ?>
                                <?php foreach ($tempRanges as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($currentRange == $value) ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Select appropriate temperature range for your aquarium type
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
                    <i class="fas fa-info-circle me-2" style="color: #ffd700;"></i>
                    <span style="color: white; font-weight: 600;">Recommended Settings</span>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold mb-3" style="color: #4eac9b;">Freshwater Aquariums:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong style="color: white;">pH:</strong> <span style="color: #b0c4be;">6.5 - 7.5</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong style="color: white;">Temperature:</strong> <span style="color: #b0c4be;">22°C - 26°C</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong style="color: white;">Turbidity:</strong> <span style="color: #b0c4be;">Below 10 NTU</span>
                        </li>
                    </ul>

                    <h6 class="fw-semibold mb-3 mt-4" style="color: #4eac9b;">Saltwater Aquariums:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong style="color: white;">pH:</strong> <span style="color: #b0c4be;">8.0 - 8.4</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong style="color: white;">Temperature:</strong> <span style="color: #b0c4be;">24°C - 28°C</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong style="color: white;">Turbidity:</strong> <span style="color: #b0c4be;">Below 5 NTU</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // Handle form submission
    $(document).ready(function() {
        $('#settingsForm').on('submit', function(e) {

            console.log('Form submit intercepted'); // sanity check

            
            console.log('Form submitted via AJAX');
            
            // Get form data
            const formData = $(this).serialize();
            console.log('Form data:', formData);
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...');
            submitBtn.prop('disabled', true);
            
            // Send AJAX request
            $.ajax({
                url: '<?= base_url('dashboard/update-settings') ?>',
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function(xhr) {
                    // Add CSRF token
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                },
                success: function(response) {
                    console.log('AJAX response:', response);
                    if (response.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', response.message);
                        } else {
                            alert('Success: ' + response.message);
                        }
                    } else {
                        let errorMessage = 'Failed to save settings';
                        if (response.errors) {
                            errorMessage = Object.values(response.errors).join('\n');
                        } else if (response.message) {
                            errorMessage = response.message;
                        }
                        
                        if (typeof showToast === 'function') {
                            showToast('error', errorMessage);
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    
                    const errorMessage = 'Network error. Please try again.';
                    if (typeof showToast === 'function') {
                        showToast('error', errorMessage);
                    } else {
                        alert('Error: ' + errorMessage);
                    }
                },
                complete: function() {
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }
            });
            
            return false; // Prevent default form submission
        });
    });

    function showToast(type, message) {
    // Create a simple toast notification element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Add to the page
    document.body.appendChild(toast);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}
</script>
<?= $this->endSection() ?>