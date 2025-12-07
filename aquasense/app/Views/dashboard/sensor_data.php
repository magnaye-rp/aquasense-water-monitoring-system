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
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .badge-temperature {
            background-color: #0dcaf0;
        }
        .badge-ph {
            background-color: #198754;
        }
        .badge-turbidity {
            background-color: #ffc107;
        }
        .export-btn {
            border-radius: 20px;
            padding: 8px 20px;
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
                        <a class="nav-link active" href="<?= base_url('dashboard/sensor-data') ?>">
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
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-database me-2"></i> Sensor Readings
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <select class="form-select form-select-sm" id="periodSelect">
                                    <option value="24h" <?= $period == "24h" ? "selected" : "" ?>>Last 24 Hours</option>
                                    <option value="7d" <?= $period == "7d" ? "selected" : "" ?>>Last 7 Days</option>
                                    <option value="30d" <?= $period == "30d" ? "selected" : "" ?>>Last 30 Days</option>
                                </select>
                            </div>
                            <button class="btn btn-light btn-sm export-btn" onclick="exportData()">
                                <i class="fas fa-download me-1"></i> Export CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($readings)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No sensor data available</h5>
                                <p class="text-muted">Check your sensor connections</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Temperature (°C)</th>
                                            <th>pH Level</th>
                                            <th>Turbidity (NTU)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($readings as $reading): ?>
                                            <tr>
                                                <td><?= date("M d, H:i", strtotime($reading["created_at"])) ?></td>
                                                <td>
                                                    <span class="badge badge-temperature"><?= number_format($reading["temperature"], 1) ?>°C</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-ph"><?= number_format($reading["ph_level"], 2) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-turbidity"><?= number_format($reading["turbidity"], 0) ?> NTU</span>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Simple status indicator
                                                    $status = "normal";
                                                    if ($reading["temperature"] < 20 || $reading["temperature"] > 30) {
                                                        $status = "warning";
                                                    }
                                                    if ($reading["ph_level"] < 6.5 || $reading["ph_level"] > 8.5) {
                                                        $status = "danger";
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?= $status ?>">
                                                        <?= ucfirst($status) ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Period change handler
        document.getElementById("periodSelect").addEventListener("change", function () {
            const period = this.value;
            window.location.href = "<?= base_url('dashboard/sensor-data') ?>?period=" + period;
        });

        // Export data function
        function exportData() {
            alert("Export feature coming soon!");
            // In a real implementation, this would download a CSV file
        }

        // Auto-refresh every 30 seconds
        setInterval(function () {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>