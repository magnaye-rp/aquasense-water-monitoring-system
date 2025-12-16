<?= $this->include('layout/header') ?>

<?php
// Define the function directly in the view if helper isn't loading
if (!function_exists('get_status_badge')) {
    function get_status_badge($status, $withIcon = false)
    {
        $status = strtolower(trim($status));
        
        $badges = [
            'normal' => [
                'class' => 'status-badge',
                'text' => 'Normal',
                'icon' => 'fa-check-circle'
            ],
            'good' => [
                'class' => 'status-badge',
                'text' => 'Good',
                'icon' => 'fa-check-circle'
            ],
            'warning' => [
                'class' => 'warning',
                'text' => 'Warning',
                'icon' => 'fa-exclamation-triangle'
            ],
            'danger' => [
                'class' => 'danger',
                'text' => 'Critical',
                'icon' => 'fa-times-circle'
            ],
            'critical' => [
                'class' => 'danger',
                'text' => 'Critical',
                'icon' => 'fa-times-circle'
            ],
            'no_data' => [
                'class' => 'secondary',
                'text' => 'No Data',
                'icon' => 'fa-question-circle'
            ]
        ];
        
        $config = $badges[$status] ?? $badges['normal'];
        
        $iconHtml = '';
        if ($withIcon) {
            $iconHtml = '<i class="fas ' . $config['icon'] . ' me-1"></i>';
        }
        
        return '<span class="badge ' . $config['class'] . '">' . $iconHtml . $config['text'] . '</span>';
    }
}
?>

<style>
/* Custom CSS for white text and black background */
.card {
    background: #000000 !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white !important;
}

.card-header {
    background: rgba(0, 0, 0, 0.8) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    color: white !important;
}

.card-body {
    background: #000000 !important;
}

.table {
    color: white !important;
    background: #000000 !important;
    border-color: rgba(255, 255, 255, 0.2);
}

.table thead th {
    border-bottom: 2px solid rgba(255, 255, 255, 0.3);
    color: white !important;
    font-weight: 600;
    background: rgba(0, 0, 0, 0.8);
}

.table tbody td {
    border-color: rgba(255, 255, 255, 0.2);
    background: #000000 !important;
}

.table-hover tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: white !important;
}


.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate,
.dataTables_wrapper .dataTables_paginate .paginate_button {
    color: white !important;
    background: transparent !important;
}

/* ================================
   DataTables – Side Label Search
   ================================ */

/* Container */
.dataTables_wrapper .dataTables_filter {
    width: 100% !important;
    float: none !important;
    display: block !important;
    text-align: left !important;
    margin-bottom: 1rem;
}

/* Label becomes horizontal (side-by-side) */
.dataTables_wrapper .dataTables_filter label {
    width: 100% !important;
    display: flex !important;
    align-items: center;
    gap: 0.75rem;
    margin: 0 !important;
    white-space: nowrap;
}

/* Input expands to remaining space */
.dataTables_wrapper .dataTables_filter input {
    flex: 1 1 auto;
    width: 100% !important;
    max-width: 100% !important;

    padding: 0.75rem 1rem;
    font-size: 1rem;

    background-color: rgba(255, 255, 255, 0.12);
    color: #ffffff;

    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 0.5rem;

    margin-left: 0 !important;
    box-sizing: border-box !important;

    transition: background-color 0.2s ease,
                border-color 0.2s ease,
                box-shadow 0.2s ease;
}

/* Focus state */
.dataTables_wrapper .dataTables_filter input:focus {
    background-color: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.5);
    color: #ffffff;
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
    outline: none;
}

/* Placeholder */
.dataTables_wrapper .dataTables_filter input::placeholder {
    color: rgba(255, 255, 255, 0.6);
}


.dataTables_wrapper .dataTables_length select {
    background-color: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_length select:focus {
    background-color: rgba(255, 255, 255, 0.15) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    color: white !important;
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    margin-left: 2px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--bs-accent) !important;
    border-color: var(--bs-accent) !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background: rgba(255, 255, 255, 0.05) !important;
    color: rgba(255, 255, 255, 0.5) !important;
}

/* Custom filter controls */
.input-group-text {
    background-color: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: white !important;
}

.form-control, .form-select {
    background-color: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: white !important;
}

.form-control:focus, .form-select:focus {
    background-color: rgba(255, 255, 255, 0.15) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    color: white !important;
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6) !important;
}

.btn-outline-secondary {
    color: white !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
}

.btn-outline-secondary:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: white !important;
}

/* Badge styling */
.badge {
    color: white !important;
}

/* Modal styling */
.modal-content {
    background: #000000 !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white !important;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.modal-body {
    color: white !important;
}

.btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* DataTable buttons styling */
.dt-buttons .btn {
    color: white !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
    background: rgba(255, 255, 255, 0.1) !important;
}

.dt-buttons .btn:hover {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
}

/* Custom show entries dropdown */
.show-entries-container {
    margin-left: auto;
    display: flex;
    align-items: center;
}

.show-entries-container label {
    color: white !important;
    margin-right: 10px;
    margin-bottom: 0;
}

.show-entries-container select {
    width: auto !important;
    min-width: 80px;
}

/* Status badges with white text */
.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.bg-secondary {
    background-color: #6c757d !important;
    color: white !important;
}

.status-badge {
    background-color: #28a745 !important;
    color: white !important;
}

.bg-accent {
    background-color: var(--bs-accent) !important;
    color: white !important;
}

/* Info box */
.bg-light {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: rgba(255, 255, 255, 0.9) !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Ensure table rows are black */
#sensorReadingsTable tbody tr {
    background-color: #000000 !important;
}

#sensorReadingsTable tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
}

/* DataTable processing overlay */
.dataTables_processing {
    background: rgba(0, 0, 0, 0.8) !important;
    color: white !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
    background: #000000;
}

.table-responsive::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.table-responsive::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}

/* Filter controls - adjust for 4 columns */
.filter-row .col-md-3 {
    flex: 0 0 auto;
    width: 24%; /* Slightly less than 25% to account for gutters */
    margin-right: 1%;
}

.filter-row .col-md-3:last-child {
    margin-right: 0;
}

/* ================================
   DataTables Pagination Styling
   ================================ */

/* Base pagination buttons */
.dataTables_wrapper .pagination .page-link {
    background-color: transparent !important;
    color: #60D1C6 !important; /* verdigris text */
    border: 1px solid rgba(96, 209, 198, 0.35) !important;
    border-radius: 0.5rem;
    margin: 0 0.25rem;
}

/* Hover */
.dataTables_wrapper .pagination .page-link:hover {
    background-color: rgba(96, 209, 198, 0.15) !important;
    color: #ffffff !important;
    border-color: #60D1C6 !important;
}

/* Active page */
.dataTables_wrapper .pagination .page-item.active .page-link {
    background-color: #249B87 !important;
    border-color: #249B87 !important;
    color: #ffffff !important;
}

/* Disabled (prev/next when unavailable) */
.dataTables_wrapper .pagination .page-item.disabled .page-link {
    background-color: transparent !important;
    color: rgba(255, 255, 255, 0.35) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}

/* Focus (keyboard / click) */
.dataTables_wrapper .pagination .page-link:focus {
    box-shadow: 0 0 0 0.25rem rgba(96, 209, 198, 0.35);
}

/* Action buttons in table */
#sensorReadingsTable .view-details {
    color: #60D1C6 !important;       /* Verdigris text */
    border-color: rgba(96, 209, 198, 0.5) !important; /* optional border */
    background-color: transparent !important;
    transition: all 0.2s ease;
}

/* Hover effect */
#sensorReadingsTable .view-details:hover {
    color: #ffffff !important;
    background-color: rgba(96, 209, 198, 0.2) !important;
    border-color: #60D1C6 !important;
}

/* Optional: focus / active effect */
#sensorReadingsTable .view-details:focus,
#sensorReadingsTable .view-details:active {
    box-shadow: 0 0 0 0.25rem rgba(96, 209, 198, 0.25) !important;
}


</style>

<!-- Add DataTable CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <i class="fas fa-water me-2 text-accent"></i>
            <span>Sensor Readings</span>
        </div>
        <div class="badge bg-accent">
            <i class="fas fa-clock me-1"></i>
            Real-time Data
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Controls -->
        <div class="row mb-4 filter-row">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="normal">Normal</option>
                        <option value="warning">Warning</option>
                        <option value="critical">Critical</option>
                        <option value="no_data">No Data</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-thermometer-half"></i></span>
                    <select class="form-select" id="tempFilter">
                        <option value="">All Temperatures</option>
                        <option value="low">&lt; <?= $thresholds['temp_min'] ?? 20 ?>°C</option>
                        <option value="normal"><?= $thresholds['temp_min'] ?? 20 ?>-<?= $thresholds['temp_max'] ?? 30 ?>°C</option>
                        <option value="high">&gt; <?= $thresholds['temp_max'] ?? 30 ?>°C</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-tint"></i></span>
                    <select class="form-select" id="phFilter">
                        <option value="">All pH Levels</option>
                        <option value="low">&lt; <?= $thresholds['ph_min'] ?? 6.5 ?></option>
                        <option value="normal"><?= $thresholds['ph_min'] ?? 6.5 ?>-<?= $thresholds['ph_max'] ?? 8.5 ?></option>
                        <option value="high">&gt; <?= $thresholds['ph_max'] ?? 8.5 ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-water"></i></span>
                    <select class="form-select" id="turbidityFilter">
                        <option value="">All Turbidity Levels</option>
                        <option value="normal">&lt; <?= $thresholds['turbidity_max'] ?? 10 ?> NTU</option>
                        <option value="high">≥ <?= $thresholds['turbidity_max'] ?? 10 ?> NTU</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <button class="btn btn-outline-secondary w-100" id="resetFilters">
                    <i class="fas fa-redo me-1"></i> Reset All Filters
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="sensorReadingsTable">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Temperature (°C)</th>
                        <th>pH Level</th>
                        <th>Turbidity (NTU)</th>
                        <th>Status</th>
                        <th data-orderable="false">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($readings as $reading): ?>
                        <tr data-status="<?= strtolower($reading['status'] ?? 'normal') ?>"
                            data-temperature="<?= $reading['temperature'] ?>"
                            data-ph="<?= $reading['ph_level'] ?>"
                            data-turbidity="<?= $reading['turbidity'] ?>"
                            data-created-at="<?= $reading['created_at'] ?>"
                            data-id="<?= $reading['id'] ?? '' ?>">
                            <td data-order="<?= strtotime($reading['created_at']) ?>">
                                <?= date('M d, Y H:i:s', strtotime($reading['created_at'])) ?>
                            </td>
                            <td>
                                <span class="temp-value"><?= number_format($reading['temperature'], 1) ?></span>
                            </td>
                            <td>
                                <span class="ph-value"><?= number_format($reading['ph_level'], 2) ?></span>
                            </td>
                            <td><?= number_format($reading['turbidity'], 0) ?></td>
                            <td>
                                <?= get_status_badge($reading['status'] ?? 'normal', true) ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-details" 
                                        data-id="<?= $reading['id'] ?? '' ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Custom Show Entries Control -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="show-entries-container">
                    <label for="showEntries">Show:</label>
                    <select class="form-select form-select-sm" id="showEntries" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">All</option>
                    </select>
                    <span class="ms-2">entries</span>
                </div>
            </div>
        </div>
        
        <!-- Show thresholds being used -->
        <div class="mt-4 p-3 bg-light rounded">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                Current thresholds: 
                Temp: <?= $thresholds['temp_min'] ?? 20 ?>-<?= $thresholds['temp_max'] ?? 30 ?>°C | 
                pH: <?= $thresholds['ph_min'] ?? 6.5 ?>-<?= $thresholds['ph_max'] ?? 8.5 ?> | 
                Turbidity: &lt;<?= $thresholds['turbidity_max'] ?? 10 ?> NTU
            </small>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reading Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Details will be loaded here via JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Load jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Load DataTables JS AFTER jQuery -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
// Store the filter functions
var customFilters = {
    status: null,
    temp: null,
    ph: null,
    turbidity: null
};

// Initialize DataTable
var table = $('#sensorReadingsTable').DataTable({
    "order": [[0, 'desc']],
    "pageLength": 25,
    "lengthChange": false,
    "responsive": true,
    "dom": '<"row mb-3"<"col-12"f>><"row"<"col-12"tr>><"row mt-3"<"col-md-5"i><"col-md-7"p>>',
    "language": {
        "search": "<i class='fas fa-search'></i> ",
        "searchPlaceholder": "Search readings...",
        "lengthMenu": "",
        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
        "infoEmpty": "Showing 0 to 0 of 0 entries",
        "infoFiltered": "(filtered from _MAX_ total entries)",
        "zeroRecords": "No matching records found",
        "paginate": {
            "first": "<i class='fas fa-angle-double-left'></i>",
            "last": "<i class='fas fa-angle-double-right'></i>",
            "next": "<i class='fas fa-angle-right'></i>",
            "previous": "<i class='fas fa-angle-left'></i>"
        }
    },
    "columnDefs": [
        {
            "targets": [5],
            "searchable": false,
            "orderable": false
        }
    ]
});

$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    var row = table.row(dataIndex).node();
    if (!row) return true;

    if (customFilters.status && !customFilters.status(row)) return false;
    if (customFilters.temp && !customFilters.temp(row)) return false;
    if (customFilters.ph && !customFilters.ph(row)) return false;
    if (customFilters.turbidity && !customFilters.turbidity(row)) return false;

    return true;
});

// Status filter
$('#statusFilter').on('change', function () {
    var status = this.value;

    customFilters.status = status
        ? function (row) {
            return row.getAttribute('data-status') === status;
        }
        : null;

    table.draw();
});

// Temperature filter
$('#tempFilter').on('change', function () {
    var filter = this.value;
    var min = <?= $thresholds['temp_min'] ?? 20 ?>;
    var max = <?= $thresholds['temp_max'] ?? 30 ?>;

    customFilters.temp = filter
        ? function (row) {
            var temp = parseFloat(row.getAttribute('data-temperature')) || 0;
            if (filter === 'low') return temp < min;
            if (filter === 'normal') return temp >= min && temp <= max;
            if (filter === 'high') return temp > max;
            return true;
        }
        : null;

    table.draw();
});

// pH filter
$('#phFilter').on('change', function () {
    var filter = this.value;
    var min = <?= $thresholds['ph_min'] ?? 6.5 ?>;
    var max = <?= $thresholds['ph_max'] ?? 8.5 ?>;

    customFilters.ph = filter
        ? function (row) {
            var ph = parseFloat(row.getAttribute('data-ph')) || 0;
            if (filter === 'low') return ph < min;
            if (filter === 'normal') return ph >= min && ph <= max;
            if (filter === 'high') return ph > max;
            return true;
        }
        : null;

    table.draw();
});

// Turbidity filter
$('#turbidityFilter').on('change', function () {
    var filter = this.value;
    var maxTurbidity = <?= $thresholds['turbidity_max'] ?? 10 ?>;

    customFilters.turbidity = filter
        ? function (row) {
            var turbidity = parseFloat(row.getAttribute('data-turbidity')) || 0;
            if (filter === 'normal') return turbidity < maxTurbidity;
            if (filter === 'high') return turbidity >= maxTurbidity;
            return true;
        }
        : null;

    table.draw();
});

// Reset filters
$('#resetFilters').on('click', function () {
    customFilters.status = null;
    customFilters.temp = null;
    customFilters.ph = null;
    customFilters.turbidity = null;

    $('#statusFilter, #tempFilter, #phFilter, #turbidityFilter').val('');
    table.draw();
});

// Custom Show Entries Control
$('#showEntries').on('change', function() {
    var entries = parseInt($(this).val());
    table.page.len(entries).draw();
});

// View details button
$(document).on('click', '.view-details', function() {
    var row = $(this).closest('tr');
    var readingId = row.data('id');
    var timestamp = row.data('created-at');
    var temperature = row.data('temperature');
    var phLevel = row.data('ph');
    var turbidity = row.data('turbidity');
    var status = row.data('status');
    
    // Format the date
    var date = new Date(timestamp);
    var formattedDate = date.toLocaleDateString('en-US', { 
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    // Get the status badge HTML from the row
    var statusBadge = row.find('td:eq(4)').html();
    
    // Create details HTML with actual data
    var details = `
        <div class="row">
            <div class="col-12 mb-3">
                <strong><i class="fas fa-id-card text-primary"></i> Reading ID:</strong><br>
                ${readingId || 'N/A'}
            </div>
            <div class="col-12 mb-3">
                <strong><i class="fas fa-clock text-info"></i> Timestamp:</strong><br>
                ${formattedDate}
            </div>
            <div class="col-4 mb-3">
                <strong><i class="fas fa-thermometer-half text-danger"></i> Temperature:</strong><br>
                <span class="h5">${parseFloat(temperature).toFixed(1)}°C</span>
            </div>
            <div class="col-4 mb-3">
                <strong><i class="fas fa-tint text-info"></i> pH Level:</strong><br>
                <span class="h5">${parseFloat(phLevel).toFixed(2)}</span>
            </div>
            <div class="col-4 mb-3">
                <strong><i class="fas fa-water text-warning"></i> Turbidity:</strong><br>
                <span class="h5">${parseInt(turbidity)} NTU</span>
            </div>
            <div class="col-12 mb-3">
                <strong><i class="fas fa-info-circle text-success"></i> Status:</strong><br>
                ${statusBadge}
            </div>
        </div>
        <hr>
        <div class="alert ${getAlertClass(status)}">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Analysis:</strong> ${getAnalysisMessage(parseFloat(temperature), parseFloat(phLevel), parseInt(turbidity))}
        </div>
    `;
    
    $('#modalBody').html(details);
    $('#detailsModal').modal('show');
});

function getAlertClass(status) {
    switch(status) {
        case 'normal':
        case 'good':
            return 'alert-success';
        case 'warning':
            return 'alert-warning';
        case 'critical':
        case 'danger':
            return 'alert-danger';
        default:
            return 'alert-secondary';
    }
}

function getAnalysisMessage(temp, ph, turbidity) {
    var tempMin = <?= $thresholds['temp_min'] ?? 20 ?>;
    var tempMax = <?= $thresholds['temp_max'] ?? 30 ?>;
    var phMin = <?= $thresholds['ph_min'] ?? 6.5 ?>;
    var phMax = <?= $thresholds['ph_max'] ?? 8.5 ?>;
    var turbidityMax = <?= $thresholds['turbidity_max'] ?? 10 ?>;
    
    var issues = [];
    var recommendations = [];
    
    if (temp < tempMin) {
        issues.push('Low temperature (' + temp.toFixed(1) + '°C)');
        recommendations.push('Consider warming the water');
    } else if (temp > tempMax) {
        issues.push('High temperature (' + temp.toFixed(1) + '°C)');
        recommendations.push('Consider cooling the water');
    }
    
    if (ph < phMin) {
        issues.push('Low pH level (' + ph.toFixed(2) + ')');
        recommendations.push('Consider adding pH increaser');
    } else if (ph > phMax) {
        issues.push('High pH level (' + ph.toFixed(2) + ')');
        recommendations.push('Consider adding pH decreaser');
    }
    
    if (turbidity > turbidityMax) {
        issues.push('High turbidity (' + turbidity + ' NTU)');
        recommendations.push('Consider water filtration or treatment');
    }
    
    if (issues.length === 0) {
        return 'All water parameters are within normal ranges. Water quality is good.';
    } else {
        var message = 'Detected issues: ' + issues.join(', ') + '. ';
        if (recommendations.length > 0) {
            message += 'Recommendations: ' + recommendations.join(', ') + '.';
        }
        return message;
    }
}
</script>

<?= $this->include('layout/footer') ?>