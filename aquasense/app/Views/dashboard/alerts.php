<?= $this->include('layout/header') ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i> System Alerts
                </div>
                <div>
                    <button class="btn btn-light btn-sm" onclick="clearAllAlerts()">
                        <i class="fas fa-trash-alt me-1"></i> Clear All
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($alerts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">No alerts</h5>
                        <p class="text-muted">All systems are operating normally</p>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($alerts as $alert): ?>
                            <div class="list-group-item alert-item alert-<?= $alert['level'] ?> mb-2">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge bg-<?= $alert['level'] ?> me-2">
                                                <?= strtoupper($alert['level']) ?>
                                            </span>
                                            <h6 class="mb-0"><?= ucfirst($alert['type']) ?> Alert</h6>
                                            <small class="text-muted ms-2">
                                                <i class="far fa-clock me-1"></i>
                                                <?= date("M d, H:i", strtotime($alert['created_at'])) ?>
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

<?php
$custom_scripts = "
function clearAllAlerts() {
    if (confirm('Are you sure you want to clear all alerts? This action cannot be undone.')) {
        // In a real implementation, you would make an AJAX call to clear alerts
        alert('This feature would clear all alerts in a real implementation.');
        window.location.reload();
    }
}

// Auto-refresh every 30 seconds
setInterval(function () {
    window.location.reload();
}, 30000);
";
?>

<?= $this->include('layout/footer') ?>