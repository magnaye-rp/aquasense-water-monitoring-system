<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AquaSense Dashboard' ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* ===============================
    GITHUB-DARK AQUASENSE THEME WITH VERDIGRIS ACCENTS
    =============================== */
    :root {
        /* === BASE === */
        --bg-page: #0D1117;        /* page background */
        --bg-card: #161B22;        /* card background */
        --bg-muted: #21262D;       /* secondary background */
        --border-subtle: #30363D;  /* subtle borders */

        /* === VERDIGRIS ACCENTS === */
        --accent: #45B7A4;          /* main verdigris */
        --accent-dark: #2F8F83;     /* darker verdigris */
        --accent-light: #66CFC4;    /* lighter verdigris */
        --accent-soft: rgba(69, 183, 164, 0.15); /* soft highlight */

        /* === TEXT === */
        --text-primary: #FFFFFF;        /* main text */
        --text-secondary: #8B949E;  /* secondary text */
        --text-muted: #6E7681;      /* muted text */

        /* === STATUS === */
        --ok: #3FB950;               /* success */
        --warn: #D29922;             /* warning */
        --danger: #F85149;           /* danger / error */

        --border-color: #30363D;
        --chart-grid: rgba(139, 148, 158, 0.1);
    }

    /* ===============================
    GLOBAL
    =============================== */
    body {
        background-color: var(--bg-page);
        color: var(--text-primary);
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    /* ===============================
    NAVBAR
    =============================== */
    .navbar {
        background-color: #0B1117 !important;
        border-bottom: 1px solid var(--border-subtle);
    }

    .navbar-brand {
        color: var(--accent) !important;  /* Changed to Verdigris */
        font-weight: 600;
    }

    .nav-link {
        color: #FFFFFF !important;  /* Changed to white */
    }

    .nav-link.active {
        color: var(--accent) !important;  /* Changed to Verdigris */
        background-color: var(--accent-soft);
        border-radius: 8px;
        font-weight: 600;
    }

    /* ===============================
    CARDS
    =============================== */
    .card {
        background-color: #00000082; /* dark verdigris-ish card bg */
        border: 1px solid #ffffffff;
        border-radius: 14px;
        color: #E5F0EF; /* bright text */
        transition: background-color 0.3s ease;
    }

    .card:hover {
        background-color: #122725; /* slightly lighter verdigris on hover */
    }

    .card-header {
        background-color: transparent;
        border-bottom: 1px solid var(--border-subtle);
        color: #FFFFFF;  /* Changed to white */
        font-weight: 500;
    }

    /* ===============================
    STATUS BANNER & BADGES
    =============================== */
    .status-banner {
        border-left: 4px solid var(--ok);
    }

    .status-banner.warning {
        border-left-color: var(--warn);
    }

    .status-banner.danger {
        border-left-color: var(--danger);
    }

    .status-badge {
        background-color: rgba(96, 209, 198, 0.15); /* slightly transparent */
        color: #60D1C6; /* bright verdigris */
        border-radius: 8px;
        padding: 0.15rem 0.5rem;
        font-size: 0.85rem;
        border: none;
    }

    .status-badge.warning {
        background-color: rgba(255, 193, 7, 0.15);
        color: #FFC107; /* yellow */
         border-radius: 8px;
        padding: 0.15rem 0.5rem;
        font-size: 0.85rem;
        border: none;
    }

    .status-badge.danger {
        background-color: rgba(248, 81, 73, 0.15);
        color: #F85149; /* red */
         border-radius: 8px;
        padding: 0.15rem 0.5rem;
        font-size: 0.85rem;
        border: none;
    }
    
    .status-badge.secondary {
        background-color: rgba(108, 117, 125, 0.15);
        color: #6C757D; /* gray */
        border-radius: 8px;
        padding: 0.15rem 0.5rem;
        font-size: 0.85rem;
        border: none;
    }

    /* ===============================
    GAUGE TEXT CENTERING
    =============================== */
    .gauge-container {
        position: relative;
        height: 150px;
        max-width: 220px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .gauge-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        width: 100%;
        pointer-events: none;
        z-index: 1;
    }

    .gauge-primary-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1.1;
        color: #E5F0EF; /* bright text */
    }

    .gauge-unit,
    .gauge-threshold {
        font-size: 0.85rem;
        color: rgba(229, 240, 239, 0.7); /* slightly muted bright text */
    }

    .gauge-threshold {
        text-align: center;
        margin-top: 10px;
        width: 100%;
        position: absolute;
        bottom: 0;
        left: 0;
    }

    /* ===============================
    DEVICE CONTROL
    =============================== */
    .device-card.device-on {
        border-left: 4px solid var(--accent);
    }

    .device-card.device-off {
        border-left: 4px solid var(--border-subtle);
    }

    .device-icon {
        color: var(--accent);
    }

    .device-off .device-icon {
        color: var(--text-muted);
    }

    .form-check-input:checked {
        background-color: var(--accent);
        border-color: var(--accent);
    }

    /* ===============================
    CHART CONTAINER
    =============================== */
    .chart-container {
        height: 300px;
    }

    /* ===============================
    LISTS / ALERTS
    =============================== */
    .alert-item {
        border-left: 4px solid var(--border-subtle);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background-color: var(--bg-muted);
        color: var(--text-primary);
    }

    .alert-item.danger {
        border-left-color: var(--danger);
    }

    .alert-item.warning {
        border-left-color: var(--warn);
    }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
                <i class="fas fa-tint me-2"></i>
                AquaSense
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == base_url('dashboard') || strpos(current_url(), 'dashboard') !== false && strpos(current_url(), 'dashboard/') === false) ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'dashboard/sensor-data') !== false ? 'active' : '' ?>" href="<?= base_url('dashboard/sensor-data') ?>">
                            <i class="fas fa-chart-line me-2"></i> Sensor Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'dashboard/alerts') !== false ? 'active' : '' ?>" href="<?= base_url('dashboard/alerts') ?>">
                            <i class="fas fa-bell me-2"></i> Alerts
                            <?php if (isset($unreadAlerts) && $unreadAlerts > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?= $unreadAlerts ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'dashboard/devices') !== false ? 'active' : '' ?>" href="<?= base_url('dashboard/devices') ?>">
                            <i class="fas fa-cogs me-2"></i> Device Control
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), 'dashboard/settings') !== false ? 'active' : '' ?>" href="<?= base_url('dashboard/settings') ?>">
                            <i class="fas fa-sliders-h me-2"></i> Settings
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i> <?= $user->username ?? 'User' ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container-fluid mt-4">