<?php
$load_datatables = true;
?>

<?= $this->extend('layout/header') ?>

<?= $this->section('content') ?>
<style>
    /* Animated background elements */
    .bg-elements {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        overflow: hidden;
        z-index: 1;
    }

    .water-bubble {
        position: absolute;
        background: radial-gradient(circle at 30% 30%, rgba(78, 172, 155, 0.15), transparent);
        border-radius: 50%;
        opacity: 0.5;
        animation: float 6s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) translateX(0); }
        50% { transform: translateY(-20px) translateX(10px); }
    }

    @keyframes glow {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 0.8; }
    }

    .glow-line {
        position: absolute;
        height: 1px;
        background: linear-gradient(90deg, transparent, #4eac9b, transparent);
        animation: glow 3s infinite;
    }

    @keyframes rise {
        0% {
            opacity: 0;
            transform: translateY(100vh) translateX(0);
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            opacity: 0;
            transform: translateY(-100vh) translateX(100px);
        }
    }

    .bubble {
        position: absolute;
        bottom: 0;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, rgba(159, 219, 205, 0.4), rgba(78, 172, 155, 0.1));
        border: 1px solid rgba(78, 172, 155, 0.2);
        animation: rise linear infinite;
    }

    .bubble::before {
        content: '';
        position: absolute;
        top: 10%;
        left: 15%;
        width: 30%;
        height: 30%;
        background: radial-gradient(circle at center, rgba(255, 255, 255, 0.8), transparent);
        border-radius: 50%;
    }

    /* Page-specific card styles */
    .card {
        background: linear-gradient(135deg, rgba(78, 172, 155, 0.1) 0%, rgba(45, 90, 90, 0.15) 100%) !important;
        border: 2px solid #4eac9b !important;
        border-radius: 15px;
    }

    .card-header {
        background: linear-gradient(135deg, #4eac9b 0%, #2d8f7f 100%) !important;
        border: none !important;
        color: white !important;
    }

    .card-body {
        background: rgba(15, 38, 38, 0.5) !important;
    }

    /* Filter Controls */
    .input-group-text {
        background-color: rgba(78, 172, 155, 0.1) !important;
        border: 1px solid #4eac9b !important;
        color: #ffd700 !important;
    }

    .form-select {
        background-color: rgba(78, 172, 155, 0.1) !important;
        border: 1px solid #4eac9b !important;
        color: white !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
    }

    .form-select:focus {
        border-color: #4eac9b !important;
        box-shadow: 0 0 0 0.25rem rgba(78, 172, 155, 0.25) !important;
    }

    .form-select option {
        background: #1a3a3a;
        color: white;
    }

    /* Button Styles */
    .btn-outline-secondary {
        color: #4eac9b !important;
        border-color: #4eac9b !important;
    }

    .btn-outline-secondary:hover {
        background: #4eac9b !important;
        color: white !important;
    }

    .btn-outline-primary {
        color: #4eac9b !important;
        border-color: #4eac9b !important;
    }

    .btn-outline-primary:hover {
        background: #4eac9b !important;
        color: white !important;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
    }

    /* Badge Styles */
    .bg-accent {
        background: #4eac9b !important;
    }

    /* Info Box */
    .bg-light {
        background: rgba(78, 172, 155, 0.15) !important;
        border: 1px solid #4eac9b !important;
        color: #b0c4be !important;
    }

    .bg-light small {
        color: #b0c4be !important;
    }

    /* DataTable adjustments */
    #sensorReadingsTable_wrapper {
        margin-bottom: 20px;
    }

    .dataTables_scroll {
        max-height: 400px;
        overflow-y: auto;
    }

    .dataTables_scrollBody {
        min-height: 200px;
    }

    /* Add padding to bottom of card body to ensure everything is visible */
    .card-body {
        padding-bottom: 30px !important;
    }

    /* Make table more compact */
    .table td, .table th {
        padding: 12px 15px;
    }

    /* Add this for better visibility */
    .main-content .card {
        margin-top: 20px;
    }
</style>

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

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <i class="fas fa-water me-2" style="color: #ffd700;"></i>
            <span style="color: white; font-weight: 600;">Sensor Readings</span>
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

        <!-- Date Range Filter -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" class="form-control" id="startDate" 
                        value="<?= !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : '' ?>"
                        placeholder="Start Date">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" class="form-control" id="endDate" 
                        value="<?= !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : '' ?>"
                        placeholder="End Date">
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary w-100" id="applyDateFilter">
                    <i class="fas fa-filter me-1"></i> Apply Date Filter
                </button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" id="clearDateFilter">
                    <i class="fas fa-times me-1"></i> Clear Date Filter
                </button>
            </div>
        </div>

        <!-- Quick Date Presets -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-info date-preset" data-days="1">Today</button>
                    <button type="button" class="btn btn-outline-info date-preset" data-days="7">Last 7 Days</button>
                    <button type="button" class="btn btn-outline-info date-preset" data-days="30">Last 30 Days</button>
                    <button type="button" class="btn btn-outline-info date-preset" data-days="90">Last 3 Months</button>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        <?php if (!empty($start_date) || !empty($end_date) || isset($_GET['status']) || isset($_GET['temp']) || isset($_GET['ph'])): ?>
        <div class="alert alert-info mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-filter me-2"></i>
                    <strong>Active Filters:</strong>
                    <?php 
                    $activeFilters = [];
                    if (!empty($start_date)) $activeFilters[] = 'From: ' . date('M d, Y', strtotime($start_date));
                    if (!empty($end_date)) $activeFilters[] = 'To: ' . date('M d, Y', strtotime($end_date));
                    if (isset($_GET['status'])) $activeFilters[] = 'Status: ' . ucfirst($_GET['status']);
                    if (isset($_GET['temp'])) $activeFilters[] = 'Temp: ' . ucfirst($_GET['temp']);
                    if (isset($_GET['ph'])) $activeFilters[] = 'pH: ' . ucfirst($_GET['ph']);
                    echo implode(' | ', $activeFilters);
                    ?>
                </div>
                <a href="<?= current_url() ?>" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-times me-1"></i> Clear All
                </a>
            </div>
        </div>
        <?php endif; ?>
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
                </tbody>
            </table>
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

<button id="backToTop" class="btn btn-accent" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: none; background: #4eac9b; color: white; border-radius: 50%; width: 50px; height: 50px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
    <i class="fas fa-arrow-up"></i>
</button>

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
<?= $this->endSection() ?>

<?= $this->include('layout/footer') ?>

<script>
// Test if jQuery is loaded
if (typeof jQuery === 'undefined') {
    console.error('jQuery is NOT loaded! Loading it now...');
    
    // Try to load jQuery dynamically
    var script = document.createElement('script');
    script.src = 'https://code.jquery.com/jquery-3.6.4.min.js';
    script.onload = function() {
        console.log('jQuery loaded dynamically');
        initializePage();
    };
    document.head.appendChild(script);
} else {
    console.log('jQuery is already loaded, version:', $.fn.jquery);
    initializePage();
}

function initializePage() {
    $(document).ready(function() {
        console.log('Page initialized with jQuery');
        // Your existing JavaScript code here
    });
}
</script>

<!-- Page-specific JavaScript -->
<script>
    (function() {
        // Configuration variables
        var thresholds = {
            tempMin: <?= $thresholds['temp_min'] ?? 20 ?>,
            tempMax: <?= $thresholds['temp_max'] ?? 30 ?>,
            phMin: <?= $thresholds['ph_min'] ?? 6.5 ?>,
            phMax: <?= $thresholds['ph_max'] ?? 8.5 ?>,
            turbidityMax: <?= $thresholds['turbidity_max'] ?? 100 ?>
        };
        
        var table; // DataTable instance

        // Initialize page when jQuery is ready
        function initPage() {
            $(document).ready(function() {
                console.log('Page initialized with jQuery');
                
                // Check for Bootstrap modal function
                if (typeof $.fn.modal === 'undefined') {
                    console.warn('Bootstrap modal not available. Loading Bootstrap...');
                    loadBootstrap();
                } else {
                    initDataTable();
                }
            });
        }

        // Load Bootstrap dynamically if needed
        function loadBootstrap() {
            // Load Bootstrap JS if not already loaded
            if (typeof bootstrap === 'undefined') {
                $.getScript('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js')
                    .done(function() {
                        console.log('Bootstrap loaded successfully');
                        initDataTable();
                    })
                    .fail(function() {
                        console.error('Failed to load Bootstrap');
                        initDataTable();
                    });
            }
        }

        // Initialize DataTable
        function initDataTable() {
            console.log('Initializing DataTable...');
            
            // Check if DataTables is loaded
            if (typeof $.fn.DataTable === 'undefined') {
                console.log('Loading DataTables...');
                loadDataTables();
            } else {
                initializeDataTable();
            }
        }

        // Load DataTables dynamically
        function loadDataTables() {
            $.getScript('https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js')
                .done(function() {
                    $.getScript('https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js')
                        .done(function() {
                            console.log('DataTables loaded successfully');
                            initializeDataTable();
                        })
                        .fail(function() {
                            console.error('Failed to load DataTables Bootstrap');
                            initializeDataTable();
                        });
                })
                .fail(function() {
                    console.error('Failed to load DataTables');
                    alert('Failed to load required libraries. Please refresh the page.');
                });
        }

        // Main DataTable initialization
        function initializeDataTable() {
            console.log('Setting up DataTable configuration...');
            
            // Set up event handlers before table initialization
            setupEventHandlers();
            
            // Initialize DataTable
            table = $('#sensorReadingsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: window.location.href,
                    type: 'GET',
                    data: function(d) {
                        // Add custom filter parameters
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                        d.status_filter = $('#statusFilter').val();
                        d.temp_filter = $('#tempFilter').val();
                        d.ph_filter = $('#phFilter').val();
                        
                        console.log('Sending filter data:', {
                            start_date: d.start_date,
                            end_date: d.end_date,
                            status_filter: d.status_filter,
                            temp_filter: d.temp_filter,
                            ph_filter: d.ph_filter
                        });
                    },
                    error: handleAjaxError
                },
                order: [[0, 'desc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                responsive: true,
                columns: [
                    { data: '0', orderable: true },
                    { data: '1', orderable: true },
                    { data: '2', orderable: true },
                    { data: '3', orderable: true },
                    { data: '4', orderable: true },
                    { 
                        data: '5', 
                        orderable: false, 
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                createdRow: function(row, data) {
                    // Add data attributes to the row
                    if (data.DT_RowAttr) {
                        $.each(data.DT_RowAttr, function(key, value) {
                            $(row).attr(key, value);
                        });
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row mt-2"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "<i class='fas fa-search'></i> ",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    zeroRecords: "No matching records found",
                    paginate: {
                        first: "<i class='fas fa-angle-double-left'></i>",
                        last: "<i class='fas fa-angle-double-right'></i>",
                        next: "<i class='fas fa-angle-right'></i>",
                        previous: "<i class='fas fa-angle-left'></i>"
                    }
                }
            });
            
            console.log('DataTable initialized successfully');
        }

        // AJAX error handler
        function handleAjaxError(xhr, error, thrown) {
            console.error('DataTables AJAX error:', error, thrown);
            
            if (xhr.responseText) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        showAlert('Error loading data: ' + response.error, 'danger');
                    }
                } catch (e) {
                    showAlert('Error loading sensor data. Please try again.', 'danger');
                }
            }
        }

        // Show alert message
        function showAlert(message, type) {
            var alertClass = 'alert-' + (type || 'danger');
            var alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Prepend to container or show as toast
            $('.container').prepend(alertHtml);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }

        // Setup all event handlers
        function setupEventHandlers() {
            // Refresh table function
            function refreshTable() {
                if (table) {
                    table.ajax.reload(null, false);
                }
            }

            // Filter handlers
            $('#statusFilter, #tempFilter, #phFilter').on('change', refreshTable);
            $('#applyDateFilter').on('click', refreshTable);

            // Enter key in date fields
            $('#startDate, #endDate').on('keypress', function(e) {
                if (e.which === 13) {
                    refreshTable();
                }
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#statusFilter, #tempFilter, #phFilter, #startDate, #endDate').val('');
                refreshTable();
            });

            // Clear date filter
            $('#clearDateFilter').on('click', function() {
                $('#startDate, #endDate').val('');
                refreshTable();
            });

            // Quick date presets
            $('.date-preset').on('click', function() {
                var days = $(this).data('days');
                var endDate = new Date();
                var startDate = new Date();
                startDate.setDate(startDate.getDate() - days);
                
                $('#startDate').val(formatDate(startDate));
                $('#endDate').val(formatDate(endDate));
                refreshTable();
            });

            // View details modal
            $(document).on('click', '.view-details', function(e) {
                e.preventDefault();
                showDetailsModal($(this).closest('tr'));
            });
        }

        // Show details modal
        function showDetailsModal(row) {
            var readingId = row.attr('data-id') || '';
            var timestamp = row.attr('data-created-at') || '';
            var temperature = parseFloat(row.attr('data-temperature') || 0);
            var phLevel = parseFloat(row.attr('data-ph') || 0);
            var turbidity = parseFloat(row.attr('data-turbidity') || 0);
            var statusBadge = row.find('td:eq(4)').html() || '<span class="badge bg-secondary">Unknown</span>';
            
            // Format date
            var formattedDate = 'Invalid Date';
            if (timestamp) {
                var date = new Date(timestamp);
                if (!isNaN(date.getTime())) {
                    formattedDate = date.toLocaleDateString('en-US', { 
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }
            }
            
            // Create modal content
            var details = `
                <div class="row">
                    <div class="col-12 mb-3">
                        <strong><i class="fas fa-id-card text-primary"></i> Reading ID:</strong><br>
                        <code class="fs-6">${readingId || 'N/A'}</code>
                    </div>
                    <div class="col-12 mb-3">
                        <strong><i class="fas fa-clock text-primary"></i> Timestamp:</strong><br>
                        ${formattedDate}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong><i class="fas fa-thermometer-half text-danger"></i> Temperature:</strong><br>
                        <span class="h5 text-primary">${temperature.toFixed(1)}°C</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong><i class="fas fa-tint text-primary"></i> pH Level:</strong><br>
                        <span class="h5 text-primary">${phLevel.toFixed(2)}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong><i class="fas fa-water text-warning"></i> Turbidity:</strong><br>
                        <span class="h5 text-primary">${Math.round(turbidity)} NTU</span>
                    </div>
                    <div class="col-12">
                        <strong><i class="fas fa-info-circle text-primary"></i> Status:</strong><br>
                        <div class="mt-2">${statusBadge}</div>
                    </div>
                </div>
            `;
            
            // Set modal content
            $('#modalBody').html(details);
            
            // Show modal - check if Bootstrap modal is available
            if (typeof $.fn.modal !== 'undefined') {
                $('#detailsModal').modal('show');
            } else {
                // Fallback: show in alert or create simple modal
                showFallbackModal(details);
            }
        }

        // Fallback modal if Bootstrap not available
        function showFallbackModal(content) {
            // Remove any existing fallback modal
            $('#fallbackModal').remove();
            
            // Create simple modal
            var modalHtml = `
                <div id="fallbackModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div style="background: white; border-radius: 8px; padding: 20px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h5>Sensor Reading Details</h5>
                            <button id="closeFallbackModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
                        </div>
                        <div id="fallbackModalContent"></div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            $('#fallbackModalContent').html(content);
            
            // Close button handler
            $('#closeFallbackModal').on('click', function() {
                $('#fallbackModal').remove();
            });
            
            // Click outside to close
            $('#fallbackModal').on('click', function(e) {
                if (e.target.id === 'fallbackModal') {
                    $(this).remove();
                }
            });
        }

        // Helper function to format date as YYYY-MM-DD
        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        // Check for jQuery and initialize
        function checkJQuery() {
            if (window.jQuery && window.jQuery.fn) {
                console.log('jQuery loaded successfully, version:', jQuery.fn.jquery);
                initPage();
            } else {
                console.log('Waiting for jQuery...');
                setTimeout(checkJQuery, 100);
            }
        }

        // Start initialization
        checkJQuery();
    })();
</script>