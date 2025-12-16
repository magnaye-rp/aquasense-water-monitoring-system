<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AquaSense Dashboard' ?></title>
    <!-- CSRF token for AJAX requests -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
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
        
        /* Bootstrap color overrides */
        --bs-primary: #45B7A4;
        --bs-success: #28a745;
        --bs-warning: #ffc107;
        --bs-danger: #dc3545;
        --bs-secondary: #6c757d;
    }

    /* ===============================
    GLOBAL
    =============================== */
    body {
        background-color: var(--bg-page);
        color: var(--text-primary);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        min-height: 100vh;
        overflow-x: hidden;
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    /* Main Content - No sidebar offset */
    .main-content {
        padding: 30px;
        min-height: 100vh;
        position: relative;
    }

    /* ===============================
    CARDS
    =============================== */
    .card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        color: var(--text-primary);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background-color: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid var(--border-subtle);
        color: var(--text-primary);
        font-weight: 500;
        padding: 0.75rem 1rem;
    }

    .card-body {
        padding: 1rem;
    }

    /* ===============================
    STATUS BANNER & BADGES
    =============================== */
    .status-banner {
        border-left: 4px solid var(--ok);
        background: linear-gradient(90deg, rgba(63, 185, 80, 0.1), transparent);
    }

    .status-banner.warning {
        border-left-color: var(--warn);
        background: linear-gradient(90deg, rgba(210, 153, 34, 0.1), transparent);
    }

    .status-banner.danger {
        border-left-color: var(--danger);
        background: linear-gradient(90deg, rgba(248, 81, 73, 0.1), transparent);
    }

    .status-banner.secondary {
        border-left-color: var(--text-secondary);
        background: linear-gradient(90deg, rgba(139, 148, 158, 0.1), transparent);
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    .bg-success {
        background-color: var(--ok) !important;
    }

    .bg-warning {
        background-color: var(--warn) !important;
    }

    .bg-danger {
        background-color: var(--danger) !important;
    }

    .bg-secondary {
        background-color: var(--text-secondary) !important;
    }

    /* ===============================
    GAUGE TEXT CENTERING
    =============================== */
    .gauge-container {
        position: relative;
        height: 150px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    canvas {
        max-width: 100% !important;
        height: auto !important;
    }

    .gauge-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 1;
    }

    .gauge-primary-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--text-primary);
    }

    .gauge-unit,
    .gauge-threshold {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .gauge-threshold {
        margin-top: 0.5rem;
        text-align: center;
    }

    /* ===============================
    DEVICE CONTROL
    =============================== */
    .device-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .device-card:hover {
        border-color: var(--accent) !important;
    }

    .device-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .form-check-input:checked {
        background-color: var(--accent);
        border-color: var(--accent);
    }

    .form-check-input {
        width: 2.5em !important;
        height: 1.25em !important;
    }

    /* ===============================
    CHART CONTAINER
    =============================== */
    .chart-container {
        height: 300px;
        position: relative;
    }

    /* ===============================
    LISTS / ALERTS
    =============================== */
    .alert-item {
        border-left: 3px solid var(--border-subtle);
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background-color: var(--bg-card);
        border-radius: 6px;
    }

    .alert-item.danger {
        border-left-color: var(--danger);
    }

    .alert-item.warning {
        border-left-color: var(--warn);
    }

    /* ===============================
    TABLES
    =============================== */
    .table {
        color: var(--text-primary);
        background-color: var(--bg-card);
    }

    .table thead th {
        border-bottom: 2px solid var(--border-subtle);
        color: var(--text-secondary);
        font-weight: 600;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .table tbody td {
        border-color: var(--border-subtle);
        background-color: transparent;
    }

    .table-hover tbody tr:hover {
        background-color: var(--accent-soft) !important;
    }

    /* ===============================
    BUTTONS
    =============================== */
    .btn {
        border-radius: 6px;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
    }

    .btn-outline-primary {
        color: var(--accent);
        border-color: var(--accent);
    }

    .btn-outline-primary:hover {
        background-color: var(--accent);
        border-color: var(--accent);
        color: white;
    }

    .btn-outline-secondary {
        color: var(--text-secondary);
        border-color: var(--border-subtle);
    }

    .btn-outline-secondary:hover {
        background-color: var(--bg-muted);
        border-color: var(--border-subtle);
        color: var(--text-primary);
    }

    /* ===============================
    FORM CONTROLS
    =============================== */
    .form-control,
    .form-select {
        background-color: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--text-primary);
    }

    .form-control:focus,
    .form-select:focus {
        background-color: var(--bg-card);
        border-color: var(--accent);
        color: var(--text-primary);
        box-shadow: 0 0 0 0.2rem rgba(69, 183, 164, 0.25);
    }

    .form-control::placeholder {
        color: var(--text-muted);
    }

    .input-group-text {
        background-color: var(--bg-muted);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
    }

    /* ===============================
    MODALS
    =============================== */
    .modal-content {
        background-color: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--text-primary);
    }

    .modal-header {
        border-bottom: 1px solid var(--border-subtle);
    }

    .modal-footer {
        border-top: 1px solid var(--border-subtle);
    }

    .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    /* ===============================
    RESPONSIVE
    =============================== */
    @media (max-width: 768px) {
        .gauge-primary-value {
            font-size: 1.5rem;
        }
        
        .card-body {
            padding: 0.75rem;
        }
    }
    </style>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    
    <!-- Loading indicator for DataTables -->
    <style>
    .dataTables_processing {
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%);
        width: 200px !important;
        margin-left: -100px !important;
        margin-top: -26px !important;
        text-align: center;
        padding: 1em 0;
        z-index: 1000;
    }
    </style>
</head>
<body>
    <!-- Main Content Container -->
    <div class="main-content" id="mainContent">
        <!-- Your page content goes here -->
    </div>
</body>
</html>