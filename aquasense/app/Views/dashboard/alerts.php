<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
            color: #0d6efd !important;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .alert-item {
            border-left: 4px solid;
            transition: all 0.3s;
        }
        .alert-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .alert-danger {
            border-left-color: #dc3545;
        }
        .alert-warning {
            border-left-color: #ffc107;
        }
        .alert-info {
            border-left-color: #0dcaf0;
        }
        .mark-read-btn {
            opacity: 0;
            transition: opacity 0.3s;
        }
        .alert-item:hover .mark-read-btn {
            opacity: 1;
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
                        <a class="nav-link active" href="<?= base_url('dashboard/alerts') ?>">
                            <i class="fas fa-bell me-1"></i> Alerts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard/devices') ?>">
                            <i class="fas fa-cogs me-1"></i> Device Control
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard/settings') ?>">
                            <i class="fas fa-sliders-h me-1"></i> Settings
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= $user->username ?? "User" ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <div>
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
                            <?php if ($pager->getPageCount() > 1): ?>
                                <nav aria-label="Page navigation" class="mt-4">
                                    <?= $pager->links() ?>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function clearAllAlerts() {
            if (confirm("Are you sure you want to clear all alerts? This action cannot be undone.")) {
                // In a real implementation, you would make an AJAX call to clear alerts
                alert("This feature would clear all alerts in a real implementation.");
                window.location.reload();
            }
        }

        // Auto-refresh every 30 seconds
        setInterval(function () {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>