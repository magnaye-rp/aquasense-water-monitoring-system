    </div> <!-- Close container-fluid -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Global JavaScript Functions - IN FOOTER -->
    <script>
    /* ===============================
    GLOBAL DEVICE CONTROL FUNCTIONS
    =============================== */
    
    // Global controlDevice function - accessible from all pages
    window.controlDevice = function(device, action) {
        console.log('Attempting to control: ' + device + ' -> ' + action);
        
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const url = '/dashboard/control-device';
        
        // Disable switch temporarily
        const switchId = device === 'oxygenator' ? 'oxySwitch' : 'pumpSwitch';
        const switchElement = document.getElementById(switchId);
        if (switchElement) {
            switchElement.disabled = true;
        }
        
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                device: device,
                action: action,
                csrf_test_name: csrfToken
            },
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response);
                
                if (response.success) {
                    showToast('success', response.message);
                    
                    // Update switch state based on response
                    if (switchElement) {
                        switchElement.checked = action === 'on';
                        switchElement.disabled = false;
                        
                        // Update label text
                        const label = switchElement.nextElementSibling;
                        if (label && label.tagName === 'LABEL') {
                            label.textContent = action === 'on' ? 'ON' : 'OFF';
                        }
                    }
                    
                    // Update card styling
                    updateDeviceCard(device, action);
                    
                } else {
                    showToast('error', 'Error: ' + response.message);
                    // Revert switch state
                    if (switchElement) {
                        switchElement.checked = !switchElement.checked;
                        switchElement.disabled = false;
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showToast('error', 'Network error. Please try again.');
                
                // Revert switch state
                if (switchElement) {
                    switchElement.checked = !switchElement.checked;
                    switchElement.disabled = false;
                }
            }
        });
    }
    
    // Update device card styling
    function updateDeviceCard(device, action) {
        const deviceName = device === 'oxygenator' ? 'Oxygenator' : 'Water Pump';
        const cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            const title = card.querySelector('.card-title');
            if (title && title.textContent.includes(deviceName)) {
                // Update border color
                card.classList.remove('border-success', 'border-secondary');
                card.classList.add(action === 'on' ? 'border-success' : 'border-secondary');
                
                // Update icon color
                const icon = card.querySelector('.device-icon, .fa-wind, .fa-tint');
                if (icon) {
                    icon.classList.remove('text-success', 'text-secondary');
                    icon.classList.add(action === 'on' ? 'text-success' : 'text-secondary');
                }
            }
        });
    }
    
    // Global toast notification function
    window.showToast = function(type, message) {
        // Remove any existing toasts
        document.querySelectorAll('.toast').forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.style.minWidth = '300px';
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
    
    // Initialize tooltips when DOM is ready
    $(document).ready(function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
        
        // Check if controlDevice function is available
        if (typeof controlDevice === 'undefined') {
            console.warn('controlDevice function not found. Make sure it\'s included in the footer.');
        }
    });
    </script>
    
    <?php if (isset($scripts) && !empty($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($custom_scripts)): ?>
        <script>
            <?= $custom_scripts ?>
        </script>
    <?php endif; ?>
</body>
</html>