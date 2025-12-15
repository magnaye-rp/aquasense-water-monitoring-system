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
                'class' => 'bg-warning',
                'text' => 'Warning',
                'icon' => 'fa-exclamation-triangle'
            ],
            'danger' => [
                'class' => 'bg-danger',
                'text' => 'Critical',
                'icon' => 'fa-times-circle'
            ],
            'critical' => [
                'class' => 'bg-danger',
                'text' => 'Critical',
                'icon' => 'fa-times-circle'
            ],
            'no_data' => [
                'class' => 'bg-secondary',
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
<style>
/* Custom CSS for dark theme */
.card {
    background: var(--bg-card) !important;
    border: 1px solid var(--border-subtle);
    color: var(--text-primary) !important;
}

.card-header {
    background: rgba(0, 0, 0, 0.2) !important;
    border-bottom: 1px solid var(--border-subtle);
    color: var(--text-primary) !important;
}

.card-body {
    background: var(--bg-card) !important;
}

.table {
    color: var(--text-primary) !important;
    background: var(--bg-card) !important;
    border-color: var(--border-subtle);
}

.table thead th {
    border-bottom: 2px solid var(--border-subtle);
    color: var(--text-primary) !important;
    font-weight: 600;
    background: rgba(0, 0, 0, 0.1);
}

.table tbody td {
    border-color: var(--border-subtle);
    background: var(--bg-card) !important;
}

.table-hover tbody tr:hover {
    background-color: var(--accent-soft) !important;
    color: var(--text-primary) !important;
}

/* DataTable specific styling */
.dataTables_wrapper {
    color: var(--text-primary) !important;
    background: var(--bg-card) !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate,
.dataTables_wrapper .dataTables_paginate .paginate_button {
    color: var(--text-primary) !important;
    background: transparent !important;
}

.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    background-color: var(--bg-card) !important;
    border: 1px solid var(--border-subtle) !important;
    color: var(--text-primary) !important;
}

.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus {
    background-color: var(--bg-card) !important;
    border-color: var(--accent) !important;
    color: var(--text-primary) !important;
    box-shadow: 0 0 0 0.2rem rgba(69, 183, 164, 0.25);
}

.dataTables_wrapper .dataTables_filter input::placeholder {
    color: var(--text-muted) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: var(--bg-card) !important;
    border: 1px solid var(--border-subtle) !important;
    color: var(--text-primary) !important;
    margin-left: 2px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--accent-soft) !important;
    border-color: var(--accent) !important;
    color: var(--text-primary) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--accent) !important;
    border-color: var(--accent) !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background: var(--bg-muted) !important;
    color: var(--text-muted) !important;
}

/* Custom filter controls */
.input-group-text {
    background-color: var(--bg-muted) !important;
    border: 1px solid var(--border-subtle) !important;
    color: var(--text-secondary) !important;
}

.form-control, .form-select {
    background-color: var(--bg-card) !important;
    border: 1px solid var(--border-subtle) !important;
    color: var(--text-primary) !important;
}

.form-control:focus, .form-select:focus {
    background-color: var(--bg-card) !important;
    border-color: var(--accent) !important;
    color: var(--text-primary) !important;
    box-shadow: 0 0 0 0.25rem rgba(69, 183, 164, 0.25);
}

.form-control::placeholder {
    color: var(--text-muted) !important;
}

.btn-outline-secondary {
    color: var(--text-secondary) !important;
    border-color: var(--border-subtle) !important;
}

.btn-outline-secondary:hover {
    background-color: var(--bg-muted) !important;
    color: var(--text-primary) !important;
}

/* Badge styling */
.badge {
    color: white !important;
}

/* Modal styling */
.modal-content {
    background: var(--bg-card) !important;
    border: 1px solid var(--border-subtle);
    color: var(--text-primary) !important;
}

.modal-header {
    border-bottom: 1px solid var(--border-subtle);
}

.modal-footer {
    border-top: 1px solid var(--border-subtle);
}

.modal-body {
    color: var(--text-primary) !important;
}

.btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* DataTable buttons styling */
.dt-buttons .btn {
    color: var(--text-primary) !important;
    border-color: var(--border-subtle) !important;
    background: var(--bg-card) !important;
}

.dt-buttons .btn:hover {
    background-color: var(--accent-soft) !important;
    color: var(--text-primary) !important;
}

/* Custom show entries dropdown */
.show-entries-container {
    margin-left: auto;
    display: flex;
    align-items: center;
}

.show-entries-container label {
    color: var(--text-primary) !important;
    margin-right: 10px;
    margin-bottom: 0;
}

.show-entries-container select {
    width: auto !important;
    min-width: 80px;
}

/* Status badges */
.bg-warning {
    background-color: var(--warn) !important;
}

.bg-danger {
    background-color: var(--danger) !important;
}

.bg-secondary {
    background-color: var(--text-secondary) !important;
}

.bg-success {
    background-color: var(--ok) !important;
}

.bg-accent {
    background-color: var(--accent) !important;
}

/* Info box */
.bg-light {
    background-color: var(--accent-soft) !important;
    color: var(--text-primary) !important;
    border: 1px solid var(--border-subtle);
}

/* Ensure table rows use theme colors */
#sensorReadingsTable tbody tr {
    background-color: var(--bg-card) !important;
}

#sensorReadingsTable tbody tr:hover {
    background-color: var(--accent-soft) !important;
}

/* DataTable processing overlay */
.dataTables_processing {
    background: var(--bg-card) !important;
    color: var(--text-primary) !important;
    border: 1px solid var(--border-subtle);
}

/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
    background: var(--bg-page);
}

.table-responsive::-webkit-scrollbar-track {
    background: var(--bg-muted);
}

.table-responsive::-webkit-scrollbar-thumb {
    background: var(--text-secondary);
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: var(--accent);
}

/* Alert styling for modal */
.alert-success {
    background-color: rgba(63, 185, 80, 0.1);
    border-color: var(--ok);
    color: var(--text-primary);
}

.alert-warning {
    background-color: rgba(210, 153, 34, 0.1);
    border-color: var(--warn);
    color: var(--text-primary);
}

.alert-danger {
    background-color: rgba(248, 81, 73, 0.1);
    border-color: var(--danger);
    color: var(--text-primary);
}

.alert-secondary {
    background-color: var(--accent-soft);
    border-color: var(--border-subtle);
    color: var(--text-primary);
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
        <div class="row mb-4">
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
                <button class="btn btn-outline-secondary w-100" id="resetFilters">
                    <i class="fas fa-redo me-1"></i> Reset Filters
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

<!-- Add DataTable JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function() {
    // Store threshold values from PHP
    var tempMin = <?= $thresholds['temp_min'] ?? 20 ?>;
    var tempMax = <?= $thresholds['temp_max'] ?? 30 ?>;
    var phMin = <?= $thresholds['ph_min'] ?? 6.5 ?>;
    var phMax = <?= $thresholds['ph_max'] ?? 8.5 ?>;
    var turbidityMax = <?= $thresholds['turbidity_max'] ?? 100 ?>;
    
    // Initialize DataTable
    var table = $('#sensorReadingsTable').DataTable({
        "order": [[0, 'desc']],
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "responsive": true,
        "dom": '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "language": {
            "search": "<i class='fas fa-search'></i> ",
            "lengthMenu": "Show _MENU_ entries",
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
        "columns": [
            { "data": "timestamp", "orderable": true },
            { "data": "temperature", "orderable": true },
            { "data": "ph", "orderable": true },
            { "data": "turbidity", "orderable": true },
            { "data": "status", "orderable": true },
            { "data": "actions", "orderable": false, "searchable": false }
        ],
        "processing": true,
        "initComplete": function(settings, json) {
            // Add custom show entries control
            $('.dataTables_length').html(`
                <div class="show-entries-container">
                    <label for="showEntries">Show:</label>
                    <select class="form-select form-select-sm" id="showEntries">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">All</option>
                    </select>
                    <span class="ms-2">entries</span>
                </div>
            `);
            
            // Handle show entries change
            $('#showEntries').on('change', function() {
                var entries = $(this).val();
                table.page.len(entries).draw();
            });
        }
    });

    // Function to apply all filters
    function applyFilters() {
        // Remove any existing filters first
        $.fn.dataTable.ext.search = [];
        
        var statusFilter = $('#statusFilter').val();
        var tempFilter = $('#tempFilter').val();
        var phFilter = $('#phFilter').val();
        
        // Create a single filter function that checks all conditions
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var row = table.row(dataIndex).node();
            if (!row) return true;
            
            var rowStatus = $(row).data('status');
            var rowTemp = parseFloat($(row).data('temperature')) || 0;
            var rowPh = parseFloat($(row).data('ph')) || 0;
            
            var statusMatch = true;
            var tempMatch = true;
            var phMatch = true;
            
            // Check status filter
            if (statusFilter && rowStatus !== statusFilter) {
                statusMatch = false;
            }
            
            // Check temperature filter
            if (tempFilter) {
                switch(tempFilter) {
                    case 'low':
                        tempMatch = (rowTemp < tempMin);
                        break;
                    case 'normal':
                        tempMatch = (rowTemp >= tempMin && rowTemp <= tempMax);
                        break;
                    case 'high':
                        tempMatch = (rowTemp > tempMax);
                        break;
                }
            }
            
            // Check pH filter
            if (phFilter) {
                switch(phFilter) {
                    case 'low':
                        phMatch = (rowPh < phMin);
                        break;
                    case 'normal':
                        phMatch = (rowPh >= phMin && rowPh <= phMax);
                        break;
                    case 'high':
                        phMatch = (rowPh > phMax);
                        break;
                }
            }
            
            // Row must match ALL active filters
            return statusMatch && tempMatch && phMatch;
        });
        
        // Apply the filter and redraw
        table.draw();
    }

    // Status filter
    $('#statusFilter').on('change', function() {
        applyFilters();
    });

    // Temperature filter
    $('#tempFilter').on('change', function() {
        applyFilters();
    });

    // pH filter
    $('#phFilter').on('change', function() {
        applyFilters();
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#statusFilter, #tempFilter, #phFilter').val('');
        // Clear all custom filters
        $.fn.dataTable.ext.search = [];
        table.draw();
        $('#showEntries').val(25);
        table.page.len(25).draw();
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
});
</script>

<?= $this->include('layout/footer') ?>