<?= $this->extend('layout/header') ?>

<?= $this->section('content') ?>
    <!-- Animated background for alerts page -->
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
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i> System Alerts
                    </div>
                    <div>
                        <button class="btn btn-warning btn-sm me-2" onclick="deleteOldAlerts()">
                            <i class="fas fa-clock me-1"></i> Delete Old Alerts (24h+)
                        </button>
                        <button class="btn btn-light btn-sm" onclick="clearAllAlerts()">
                            <i class="fas fa-trash-alt me-1"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-6">
                            <div class="card bg-dark text-white mb-3">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Total Alerts</h6>
                                    <h2 class="text-info"><?= $totalAlerts ?? 0 ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-dark text-white mb-3">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Last 24 Hours</h6>
                                    <h2 class="text-success"><?= $recentAlerts ?? 0 ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-dark text-white mb-3">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Critical</h6>
                                    <h2 class="text-danger"><?= $criticalAlerts ?? 0 ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-dark text-white mb-3">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Old Alerts (24h+)</h6>
                                    <h2 class="text-warning"><?= $oldAlerts ?? 0 ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (empty($alerts)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h5 class="text-success">No alerts</h5>
                            <p class="text-muted">All systems are operating normally</p>
                        </div>
                    <?php else: ?>
                        <!-- Filter buttons -->
                        <div class="mb-3">
                            <button class="btn btn-sm btn-outline-info me-1 active" onclick="filterAlerts('all')">All</button>
                            <button class="btn btn-sm btn-outline-danger me-1" onclick="filterAlerts('danger')">Critical</button>
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="filterAlerts('warning')">Warnings</button>
                            <button class="btn btn-sm btn-outline-info me-1" onclick="filterAlerts('info')">Info</button>
                            <button class="btn btn-sm btn-outline-secondary me-1" onclick="filterAlerts('old')">Old (24h+)</button>
                        </div>
                        
                        <div class="list-group" id="alertsList">
                            <?php foreach ($alerts as $alert): 
                                $isOld = (time() - strtotime($alert['created_at'])) > 86400; // 24 hours in seconds
                                $oldClass = $isOld ? 'old-alert' : '';
                            ?>
                                <div class="list-group-item alert-item alert-<?= $alert['level'] ?> mb-2 <?= $oldClass ?>"
                                     data-level="<?= $alert['level'] ?>" 
                                     data-old="<?= $isOld ? 'true' : 'false' ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-<?= $alert['level'] ?> me-2">
                                                    <?= strtoupper($alert['level']) ?>
                                                    <?php if ($isOld): ?>
                                                        <i class="fas fa-clock ms-1" title="Older than 24 hours"></i>
                                                    <?php endif; ?>
                                                </span>
                                                <h6 class="mb-0"><?= ucfirst($alert['type']) ?> Alert</h6>
                                                <small class="text-muted ms-2">
                                                    <i class="far fa-clock me-1"></i>
                                                    <?= date("M d, H:i", strtotime($alert['created_at'])) ?>
                                                    <?php if ($isOld): ?>
                                                        <span class="text-warning ms-1">(Old)</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <p class="mb-1"><?= $alert['message'] ?></p>
                                        </div>
                                        <div class="ms-3">
                                            <a href="<?= base_url('dashboard/delete-alert/' . $alert['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger mark-read-btn"
                                               onclick="return confirm('Are you sure you want to delete this alert?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <?= $pager->links() ?>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Define global functions
    window.deleteOldAlerts = function() {
        if (confirm('Are you sure you want to delete all alerts older than 24 hours?')) {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfMeta) {
                showToast('error', 'CSRF token not found. Please refresh the page.');
                return;
            }
            const csrfToken = csrfMeta.getAttribute('content');
            const btn = event.target.closest('button') || event.target;
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Deleting...';
            btn.disabled = true;
            
            // Using fetch API instead of jQuery
            fetch('<?= base_url("dashboard/delete-old-alerts") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: 'csrf_test_name=' + encodeURIComponent(csrfToken)
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    if (data.deleted_count > 0) {
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                } else {
                    showToast('error', 'Error: ' + (data.message || 'Unknown error'));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Network error. Please try again. ' + error.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    }
    
    window.clearAllAlerts = function() {
        if (confirm('Are you sure you want to clear ALL alerts? This action cannot be undone.')) {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfMeta) {
                showToast('error', 'CSRF token not found. Please refresh the page.');
                return;
            }
            const csrfToken = csrfMeta.getAttribute('content');
            const btn = event.target.closest('button') || event.target;
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Clearing...';
            btn.disabled = true;
            
            fetch('<?= base_url("dashboard/clear-all-alerts") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: 'csrf_test_name=' + encodeURIComponent(csrfToken)
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('error', 'Error: ' + (data.message || 'Unknown error'));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Network error. Please try again. ' + error.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    }
    
    window.filterAlerts = function(filter) {
        // Update active button
        const filterButtons = document.querySelectorAll('.btn-sm.btn-outline-info, .btn-sm.btn-outline-danger, .btn-sm.btn-outline-warning, .btn-sm.btn-outline-secondary');
        filterButtons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.textContent.toLowerCase().includes(filter) || 
                (filter === 'all' && btn.textContent === 'All')) {
                btn.classList.add('active');
            }
        });
        
        // Show/hide alerts based on filter
        const alertItems = document.querySelectorAll('.alert-item');
        alertItems.forEach(item => {
            const level = item.getAttribute('data-level');
            const isOld = item.getAttribute('data-old') === 'true';
            
            let show = false;
            
            switch(filter) {
                case 'all':
                    show = true;
                    break;
                case 'danger':
                    show = level === 'danger';
                    break;
                case 'warning':
                    show = level === 'warning';
                    break;
                case 'info':
                    show = level === 'info';
                    break;
                case 'old':
                    show = isOld === true;
                    break;
            }
            
            if (show) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Auto-refresh every 30 seconds
    setInterval(function () {
        window.location.reload();
    }, 30000);
    </script>
<?= $this->endSection() ?>