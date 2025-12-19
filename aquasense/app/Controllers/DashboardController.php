<?php

namespace App\Controllers;

use App\Models\SensorReadingModel;
use App\Models\AlertModel;
use App\Models\DeviceLogModel;
use App\Models\SystemSettingsModel;
use App\Models\DeviceCommandModel;

class DashboardController extends BaseController
{
    protected $sensorReadingModel;
    protected $alertModel;
    protected $deviceLogModel;
    protected $systemSettingsModel;
    protected $commandModel;
    protected $session;

    public function __construct()
    {
        // Initialize services and models here.
        // Authentication/authorization is handled by route filters (see Routes.php).
        $this->session = \Config\Services::session();
        $this->commandModel = new DeviceCommandModel(); // Remove backslash
        $this->sensorReadingModel = new SensorReadingModel();
        $this->alertModel = new AlertModel();
        $this->deviceLogModel = new DeviceLogModel();
        $this->systemSettingsModel = new SystemSettingsModel();
        
    }

    public function index()
    {
        return redirect()->to(base_url('dashboard/main'));
    }

    public function main()
    {
        // Get latest sensor reading
        $latestReadings = $this->sensorReadingModel->getLatestReadings(1);
        $currentReading = !empty($latestReadings) ? $latestReadings[0] : null;

        // Get system status
        $status = $this->getSystemStatus($currentReading);

        // Get recent alerts (limit 10)
        $alerts = $this->alertModel->orderBy('created_at', 'DESC')->limit(10)->findAll();

        // Get device status
        $deviceStatus = $this->getDeviceStatus();

        // Prepare data for the view
        $data = [
            'title' => 'Dashboard - AquaSense',
            'currentReading' => $currentReading,
            'status' => $status,
            'alerts' => $alerts,
            'deviceStatus' => $deviceStatus,
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user(),
        ];

        // Ensure view file exists in app/Views/dashboard/main.php
        return view('dashboard/main', $data);
    }

    private function calculateReadingStatus($reading) // Fixed method name (camelCase)
    {
        // Get thresholds from database
        $thresholds = $this->systemSettingsModel->getThresholds(); // Use existing instance
        
        // Default thresholds if not found
        $tempMin = $thresholds['temp_min'] ?? 20;
        $tempMax = $thresholds['temp_max'] ?? 30;
        $phMin = $thresholds['ph_min'] ?? 6.5;
        $phMax = $thresholds['ph_max'] ?? 8.5;
        $turbidityMax = $thresholds['turbidity_max'] ?? 100; // Fixed: was 10, should be 100
        
        // Set warning zones (10% buffer)
        $tempWarningMin = $tempMin + 2;
        $tempWarningMax = $tempMax - 2;
        $phWarningMin = $phMin + 0.5;
        $phWarningMax = $phMax - 0.5;
        $turbidityWarning = $turbidityMax * 0.7;
        
        $isCritical = false;
        $isWarning = false;
        
        // Check temperature
        if ($reading['temperature'] <= $tempMin || $reading['temperature'] >= $tempMax) {
            $isCritical = true;
        } elseif ($reading['temperature'] <= $tempWarningMin || $reading['temperature'] >= $tempWarningMax) {
            $isWarning = true;
        }
        
        // Check pH level
        if ($reading['ph_level'] <= $phMin || $reading['ph_level'] >= $phMax) {
            $isCritical = true;
        } elseif ($reading['ph_level'] <= $phWarningMin || $reading['ph_level'] >= $phWarningMax) {
            $isWarning = true;
        }
        
        // Check turbidity
        if ($reading['turbidity'] >= $turbidityMax) {
            $isCritical = true;
        } elseif ($reading['turbidity'] >= $turbidityWarning) {
            $isWarning = true;
        }
        
        // Determine final status
        if ($isCritical) {
            return 'critical';
        } elseif ($isWarning) {
            return 'warning';
        } else {
            return 'normal';
        }
    }
    
    public function sensorData()
    {
        $thresholds = $this->systemSettingsModel->getThresholds();
        
        // Check if this is an AJAX request from DataTables
        if ($this->request->isAJAX() && $this->request->getGet('draw')) {
            return $this->getSensorDataAjax();
        }
        
        // Regular page load - return view with basic data
        $data = [
            'title' => 'Sensor Data - AquaSense', 
            'load_datatables' => true,
            'thresholds' => $thresholds,
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user(),
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date')
        ];
        
        return view('dashboard/sensor-data', $data);
    }
    
    /**
     * AJAX method for DataTables server-side processing
     */
    private function getSensorDataAjax()
    {
        try {
            // Get DataTables parameters
            $draw = $this->request->getGet('draw');
            $start = $this->request->getGet('start') ?? 0;
            $length = $this->request->getGet('length') ?? 25;
            $searchValue = $this->request->getGet('search')['value'] ?? '';
            $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? 0;
            $orderDirection = $this->request->getGet('order')[0]['dir'] ?? 'desc';
            
            // Get filter parameters
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');
            $statusFilter = $this->request->getGet('status_filter');
            $tempFilter = $this->request->getGet('temp_filter');
            $phFilter = $this->request->getGet('ph_filter');
            
            // Get thresholds for status calculation
            $thresholds = $this->systemSettingsModel->getThresholds();
            
            // Initialize query
            $builder = $this->sensorReadingModel->builder();
            
            // Get total records count (without filters)
            $totalRecords = $this->sensorReadingModel->countAllResults();
            
            // Apply date filters
            if (!empty($startDate)) {
                $builder->where('DATE(created_at) >=', date('Y-m-d', strtotime($startDate)));
            }
            
            if (!empty($endDate)) {
                $builder->where('DATE(created_at) <=', date('Y-m-d', strtotime($endDate)));
            }
            
            // Apply search
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('temperature', $searchValue)
                    ->orLike('ph_level', $searchValue)
                    ->orLike('turbidity', $searchValue)
                    ->orLike('created_at', $searchValue)
                    ->groupEnd();
            }
            
            // Get filtered count
            $filteredRecords = $builder->countAllResults(false);
            
            // Define column mapping for ordering
            $columnMap = [
                0 => 'created_at',    // Timestamp
                1 => 'temperature',   // Temperature
                2 => 'ph_level',      // pH Level
                3 => 'turbidity',     // Turbidity
                4 => 'created_at'     // Status (default to created_at for ordering)
            ];
            
            // Apply ordering
            $orderColumn = $columnMap[$orderColumnIndex] ?? 'created_at';
            $builder->orderBy($orderColumn, $orderDirection);
            
            // Apply pagination
            $builder->limit($length, $start);
            
            // Get filtered data
            $query = $builder->get();
            $readings = $query->getResultArray();
            
            // Process data for DataTables
            $data = [];
            foreach ($readings as $reading) {
                $status = $this->calculateReadingStatus($reading);
                
                // Determine badge class based on status
                $badgeClass = 'badge bg-success';
                $statusText = 'Normal';
                $icon = 'fa-check-circle';
                
                if ($status === 'warning') {
                    $badgeClass = 'badge bg-warning';
                    $statusText = 'Warning';
                    $icon = 'fa-exclamation-triangle';
                } elseif ($status === 'critical' || $status === 'danger') {
                    $badgeClass = 'badge bg-danger';
                    $statusText = 'Critical';
                    $icon = 'fa-times-circle';
                } elseif ($status === 'no_data') {
                    $badgeClass = 'badge bg-secondary';
                    $statusText = 'No Data';
                    $icon = 'fa-question-circle';
                }
                
                $data[] = [
                    'DT_RowAttr' => [
                        'data-id' => $reading['id'],
                        'data-created-at' => $reading['created_at'],
                        'data-temperature' => $reading['temperature'],
                        'data-ph' => $reading['ph_level'],
                        'data-turbidity' => $reading['turbidity'],
                        'data-status' => $status
                    ],
                    '0' => date('M d, Y H:i:s', strtotime($reading['created_at'])),
                    '1' => number_format($reading['temperature'], 1),
                    '2' => number_format($reading['ph_level'], 2),
                    '3' => number_format($reading['turbidity'], 0),
                    '4' => '<span class="' . $badgeClass . '"><i class="fas ' . $icon . ' me-1"></i>' . $statusText . '</span>',
                    '5' => '<button class="btn btn-sm btn-outline-primary view-details" data-id="' . $reading['id'] . '"><i class="fas fa-eye"></i></button>'
                ];
            }
            
            // Return JSON response
            return $this->response->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Sensor data AJAX error: ' . $e->getMessage());
            
            // Return error response
            return $this->response->setStatusCode(500)->setJSON([
                'draw' => $this->request->getGet('draw') ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while loading data. Please try again.'
            ]);
        }
    }

    /**
     * New method for date range chart data
     */
    private function getDateRangeChartData($startDate, $endDate)
    {
        $chartReadings = $this->sensorReadingModel
            ->where('DATE(created_at) >=', date('Y-m-d', strtotime($startDate)))
            ->where('DATE(created_at) <=', date('Y-m-d', strtotime($endDate)))
            ->orderBy('created_at', 'ASC')
            ->findAll();
        
        $data = [
            'labels' => [],
            'temperature' => [],
            'ph' => [],
            'turbidity' => []
        ];
        
        foreach ($chartReadings as $reading) {
            $data['labels'][] = date('M d H:i', strtotime($reading['created_at']));
            $data['temperature'][] = floatval($reading['temperature']);
            $data['ph'][] = floatval($reading['ph_level']);
            $data['turbidity'][] = floatval($reading['turbidity']);
        }
        
        return $data;
    }

    /**
     * Get chart data for view (helper method without AJAX check)
     * 
     * @param string $range Time range (24h, 48h, etc.)
     * @return array Chart data array
     */
    private function getChartDataForView($range = '24h')
    {
        try {
            // Calculate start date based on range
            $startDate = $this->calculateStartDate($range);
            
            // Get data for the range
            $chartReadings = $this->sensorReadingModel
                ->where('created_at >=', $startDate)
                ->orderBy('created_at', 'ASC')
                ->findAll();
            
            // Prepare chart data
            $data = [
                'labels' => [],
                'temperature' => [],
                'ph' => [],
                'turbidity' => []
            ];
            
            foreach ($chartReadings as $reading) {
                // Format label based on range
                $label = $this->formatLabel($reading['created_at'], $range);
                $data['labels'][] = $label;
                $data['temperature'][] = floatval($reading['temperature']);
                $data['ph'][] = floatval($reading['ph_level']);
                $data['turbidity'][] = floatval($reading['turbidity']);
            }
            
            // If no data, return empty arrays
            if (empty($chartReadings)) {
                $data = [
                    'labels' => ['No Data'],
                    'temperature' => [0],
                    'ph' => [0],
                    'turbidity' => [0]
                ];
            }
            
            return $data;
            
        } catch (\Exception $e) {
            log_message('error', 'Chart data error: ' . $e->getMessage());
            return [
                'labels' => ['No Data'],
                'temperature' => [0],
                'ph' => [0],
                'turbidity' => [0]
            ];
        }
    }

    /**
     * Get chart data for AJAX requests
     * 
     * @return \CodeIgniter\HTTP\Response JSON response
     */
    public function getChartData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $range = $this->request->getGet('range') ?? '24h';
        
        // Use helper method to get chart data
        $data = $this->getChartDataForView($range);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'range' => $range
        ]);
    }

    public function getCurrentReadings()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
    
        $range = $this->request->getGet('range') ?? '24h';
        
        try {
            $startDate = $this->calculateStartDate($range);
            
            // Get latest reading in the range
            $latestReading = $this->sensorReadingModel
                ->where('created_at >=', $startDate)
                ->orderBy('created_at', 'DESC')
                ->first();
            
            if ($latestReading) {
                $data = [
                    'temperature' => floatval($latestReading['temperature']),
                    'ph_level' => floatval($latestReading['ph_level']),
                    'turbidity' => floatval($latestReading['turbidity']),
                    'timestamp' => $latestReading['created_at']
                ];
            } else {
                // If no data in range, get the most recent reading overall
                $latestOverall = $this->sensorReadingModel
                    ->orderBy('created_at', 'DESC')
                    ->first();
                
                if ($latestOverall) {
                    $data = [
                        'temperature' => floatval($latestOverall['temperature']),
                        'ph_level' => floatval($latestOverall['ph_level']),
                        'turbidity' => floatval($latestOverall['turbidity']),
                        'timestamp' => $latestOverall['created_at']
                    ];
                } else {
                    $data = null;
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'range' => $range
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Current readings error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading current readings'
            ]);
        }
    }

    public function alerts()
    {
        // Get paginated alerts
        $alerts = $this->alertModel
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
    
        $pager = $this->alertModel->pager;
    
        // Mark alerts as read when viewing
        $this->alertModel->markAllAsRead();
    
        // Calculate date 24 hours ago
        $twentyFourHoursAgo = date('Y-m-d H:i:s', strtotime('-24 hours'));
    
        // Create a fresh model instance for counting to avoid query contamination
        $countModel = new \App\Models\AlertModel();
    
        // Get statistics using the fresh instance
        $totalAlerts = $countModel->countAll();
        
        // Count alerts from last 24 hours
        $recentAlerts = $countModel
            ->where('created_at >=', $twentyFourHoursAgo)
            ->countAllResults();
        
        // Count critical alerts
        $criticalAlerts = $countModel
            ->where('level', 'danger')
            ->countAllResults();
        
        // Count old alerts (24h+)
        $oldAlerts = $countModel
            ->where('created_at <', $twentyFourHoursAgo)
            ->countAllResults();
    
        $data = [
            'title' => 'Alerts - AquaSense',
            'alerts' => $alerts,
            'pager' => $pager,
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user(),
            // Add statistics
            'totalAlerts' => $totalAlerts,
            'recentAlerts' => $recentAlerts,
            'criticalAlerts' => $criticalAlerts,
            'oldAlerts' => $oldAlerts
        ];
    
        return view('dashboard/alerts', $data);
    }

    public function deleteOldAlerts()
{
    // Check if it's an AJAX request
    if ($this->request->isAJAX()) {
        try {
            // Calculate date 24 hours ago
            $twentyFourHoursAgo = date('Y-m-d H:i:s', strtotime('-24 hours'));
            
            // Delete alerts older than 24 hours
            $deleted = $this->alertModel
                ->where('created_at <', $twentyFourHoursAgo)
                ->delete();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Deleted $deleted alerts older than 24 hours",
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    } else {
        // Handle non-AJAX request (form submission)
        $twentyFourHoursAgo = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $deleted = $this->alertModel
            ->where('created_at <', $twentyFourHoursAgo)
            ->delete();
        
        return redirect()->back()->with('success', "Deleted $deleted alerts older than 24 hours");
    }
}

public function clearAllAlerts()
{
    if ($this->request->isAJAX()) {
        try {
            // Add a condition that's always true to be safer
            $deleted = $this->alertModel
                ->where('id >', 0)  // Always true condition for safety
                ->delete();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Cleared all $deleted alerts",
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . e->getMessage()
            ]);
        }
    } else {
        // Add a condition that's always true to be safer
        $deleted = $this->alertModel
            ->where('id >', 0)  // Always true condition for safety
            ->delete();
        return redirect()->back()->with('success', "Cleared all $deleted alerts");
    }
} 

    public function deleteAlert($id)
    {
        $this->alertModel->delete($id);
        return redirect()->back()->with('success', 'Alert deleted successfully');
    }

    public function devices()
    {
        $deviceHistory = $this->deviceLogModel
            ->orderBy('created_at', 'DESC')
            ->paginate(20);

        $pager = $this->deviceLogModel->pager;

        $currentStatus = $this->getDeviceStatus();
        $settings = $this->systemSettingsModel->getCurrentSettings();
        $autoModeStatus = $this->systemSettingsModel->getAutoModeStatus();

        $data = [
            'title' => 'Device Control - AquaSense',
            'deviceHistory' => $deviceHistory,
            'pager' => $pager,
            'currentStatus' => $currentStatus,
            'settings' => $settings,
            'autoModeStatus' => $autoModeStatus, // Added
            'latestCommands' => $this->commandModel->getLatestCommands('NODEMCU_AQUASENSE_001'), // Added
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user()
        ];

        return view('dashboard/devices', $data);
    }

    public function settings()
    {
        $settings = $this->systemSettingsModel->getCurrentSettings();
        
        // Create default settings if none exist
        if (!$settings) {
            $defaultSettings = [
                'water_type' => 'generic',
                'oxygenator_auto' => 0,
                'pump_auto' => 0,
                'oxygenator_interval' => 0,
                'pump_interval' => 0,
                'ph_good_min' => 6.5,
                'ph_good_max' => 8.5,
                'turbidity_limit' => 100,
                'temperature_range' => '22-26',
                'email_alerts' => 1
            ];
            
            $this->systemSettingsModel->insert($defaultSettings);
            $settings = $this->systemSettingsModel->getCurrentSettings();
        }

        $waterTypes = [
            'freshwater' => 'Freshwater',
            'saltwater' => 'Saltwater',
            'generic' => 'Generic'
        ];

        // Temperature ranges for dropdown
        $tempRanges = [
            '15-20' => '15°C - 20°C (Cold water fish)',
            '18-22' => '18°C - 22°C (Cool water fish)',
            '20-24' => '20°C - 24°C (Tropical fish)',
            '22-26' => '22°C - 26°C (Most freshwater)',
            '24-28' => '24°C - 28°C (Tropical community)',
            '26-30' => '26°C - 30°C (Discus/Rams)',
            '28-32' => '28°C - 32°C (Marine/coral)'
        ];

        $data = [
            'title' => 'Settings - AquaSense',
            'settings' => $settings,
            'waterTypes' => $waterTypes,
            'tempRanges' => $tempRanges, // Pass to view
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user()
        ];

        return view('dashboard/settings', $data);
    }

    public function updateSettings()
    {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        log_message('debug', '=== updateSettings() called ===');
        log_message('debug', 'Is AJAX: ' . ($this->request->isAJAX() ? 'Yes' : 'No'));
        log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
        
        if (!$this->request->isAJAX()) {
            log_message('debug', 'Not an AJAX request!');
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $postData = $this->request->getPost();
        log_message('debug', 'Processing data: ' . print_r($postData, true));

        // Validate data
        $validation = \Config\Services::validation();
        $validation->setRules([
            'water_type' => 'required|in_list[freshwater,saltwater,generic]',
            'ph_good_min' => 'required|numeric',
            'ph_good_max' => 'required|numeric',
            'turbidity_limit' => 'required|integer',
            'temperature_range' => 'required|regex_match[/^\d{2}-\d{2}$/]',
            'email_alerts' => 'permit_empty'
        ]);

        if (!$validation->run($postData)) {
            $errors = $validation->getErrors();
            log_message('debug', 'Validation errors: ' . print_r($errors, true));
            return $this->response->setJSON([
                'success' => false,
                'errors' => $errors
            ]);
        }

        try {
            // Parse boolean values
            $postData['email_alerts'] = isset($postData['email_alerts']) ? 1 : 0;
            
            log_message('debug', 'Data to save: ' . print_r($postData, true));
            
            // Update settings
            $result = $this->systemSettingsModel->updateSettings($postData);
            
            log_message('debug', 'Update result: ' . ($result ? 'Success' : 'Failed'));

            if ($result) {
                log_message('debug', 'Settings updated successfully');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Settings updated successfully'
                ]);
            } else {
                log_message('debug', 'Failed to update settings');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update settings'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Exception in updateSettings: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    public function controlDevice()
    {
        // Enable detailed error reporting temporarily
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        log_message('debug', '=== Control Device Request Start ===');
        log_message('debug', 'POST Data: ' . print_r($this->request->getPost(), true));
        log_message('debug', 'Headers: ' . print_r($this->request->getHeaders(), true));
        
        if (!$this->request->isAJAX()) {
            log_message('debug', 'Not an AJAX request');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Get POST data
        $device = $this->request->getPost('device');
        $action = $this->request->getPost('action');

        // Simple validation
        if (!$device || !$action) {
            log_message('debug', 'Missing parameters');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing device or action parameters'
            ]);
        }

        if (!in_array($device, ['oxygenator', 'water_pump'])) {
            log_message('debug', 'Invalid device: ' . $device);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid device name'
            ]);
        }

        if (!in_array(strtolower($action), ['on', 'off'])) {
            log_message('debug', 'Invalid action: ' . $action);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }

        try {
            log_message('debug', 'Attempting to add command for device: ' . $device . ', action: ' . $action);
            
            // Check for duplicate command
            $isDuplicate = $this->commandModel->isDuplicateCommand($device, $action, 'NODEMCU_AQUASENSE_001');
            log_message('debug', 'Is duplicate command: ' . ($isDuplicate ? 'Yes' : 'No'));
            
            if (!$isDuplicate) {
                // Add command to database
                $commandId = $this->commandModel->addCommand($device, strtoupper($action), 'NODEMCU_AQUASENSE_001');
                
                if ($commandId) {
                    log_message('debug', "Command added successfully. ID: {$commandId}");
                    
                    // Verify command was inserted
                    $command = $this->commandModel->find($commandId);
                    log_message('debug', 'Command record: ' . print_r($command, true));
                    
                    // Log device action
                    $logId = $this->deviceLogModel->logDeviceAction(
                        $device, 
                        strtoupper($action), 
                        'manual'
                    );
                    
                    log_message('debug', 'Device log created. ID: ' . $logId);
                    
                    // Return success response
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => ucfirst($device) . ' turned ' . strtoupper($action) . ' successfully',
                        'command_id' => $commandId,
                        'log_id' => $logId
                    ]);
                } else {
                    log_message('error', 'Failed to add command to database');
                    log_message('error', 'Command model errors: ' . print_r($this->commandModel->errors(), true));
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to save command to database'
                    ]);
                }
            } else {
                log_message('debug', 'Duplicate command prevented');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Command already exists (prevented duplicate)',
                    'duplicate' => true
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Exception in controlDevice: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleAutoMode()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        // Get POST data
        $device = $this->request->getPost('device');
        $enabled = $this->request->getPost('enabled');
        $toggleAll = $this->request->getPost('toggle_all'); // New parameter for toggling all devices

        // Log for debugging
        log_message('debug', 'Toggle auto mode request: ' . print_r($this->request->getPost(), true));

        // Validation
        if ($enabled === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing enabled parameter'
            ]);
        }

        $enabled = (int)$enabled; // Convert to integer (0 or 1)

        try {
            // Get current settings
            $settings = $this->systemSettingsModel->getCurrentSettings();
            
            if (!$settings) {
                $this->systemSettingsModel->insert([
                    'water_type' => 'generic',
                    'oxygenator_auto' => 0,
                    'pump_auto' => 0
                ]);
                $settings = $this->systemSettingsModel->getCurrentSettings();
            }
            

            // If toggle_all is set, update both devices
            if ($toggleAll) {
                $updateData = [
                    'oxygenator_auto' => $enabled,
                    'pump_auto' => $enabled
                ];
                
                $result = $this->systemSettingsModel->updateSettings($updateData);
                
                if ($result) {
                    $status = $enabled ? 'enabled' : 'disabled';
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => "Auto mode {$status} for all devices successfully",
                        'enabled' => $enabled
                    ]);
                }
            } else {
                // Single device toggle (for backward compatibility)
                if (!$device) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Missing device parameter'
                    ]);
                }

                if (!in_array($device, ['oxygenator', 'water_pump'])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid device name'
                    ]);
                }

                // Update the appropriate auto mode setting
                $fieldName = $device === 'oxygenator' ? 'oxygenator_auto' : 'pump_auto';
                $updateData = [$fieldName => $enabled];

                $result = $this->systemSettingsModel->updateSettings($updateData);

                if ($result) {
                    $status = $enabled ? 'enabled' : 'disabled';
                    $deviceName = ucfirst(str_replace('_', ' ', $device));
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => "{$deviceName} auto mode {$status} successfully",
                        'enabled' => $enabled
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update auto mode settings'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Failed to toggle auto mode: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function getCurrentData()
    {
        session_write_close();

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }

        $cacheKey = 'dashboard_current_data';

        $cache = \Config\Services::cache();
        if ($cached = $cache->get($cacheKey)) {
            return $this->response->setJSON($cached);
        }

        $latestReadings = $this->sensorReadingModel->getLatestReadings(1);
        $currentReading = $latestReadings[0] ?? null;

        $data = [
            'success' => true,
            'currentReading' => $currentReading,
            'status' => $this->getSystemStatus($currentReading),
            'deviceStatus' => $this->getDeviceStatus(),
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $cache->save($cacheKey, $data, 5); // cache 5 seconds

        return $this->response->setJSON($data);
    }


    /**
     * Private helper methods
     */
    private function getSystemStatus($currentReading)
    {
        if (!$currentReading) {
            return [
                'status' => 'no_data',
                'message' => 'No sensor data available',
                'color' => 'secondary'
            ];
        }

        $thresholds = $this->systemSettingsModel->getThresholds();
        $status = 'good';
        $message = 'All parameters within normal range';
        $color = 'success';

        // Check thresholds (with null safety)
        if (isset($currentReading['temperature'])) {
            if ($currentReading['temperature'] < ($thresholds['temp_min'] ?? 20) || 
                $currentReading['temperature'] > ($thresholds['temp_max'] ?? 30)) {
                $status = 'warning';
                $message = 'Temperature out of range';
                $color = 'warning';
            }
        }

        if (isset($currentReading['ph_level'])) {
            if ($currentReading['ph_level'] < ($thresholds['ph_min'] ?? 6.5) || 
                $currentReading['ph_level'] > ($thresholds['ph_max'] ?? 8.5)) {
                $status = 'danger';
                $message = 'pH level critical';
                $color = 'danger';
            }
        }

        if (isset($currentReading['turbidity'])) {
            if ($currentReading['turbidity'] > ($thresholds['turbidity_max'] ?? 100)) {
                $status = 'warning';
                $message = 'High turbidity detected';
                $color = 'warning';
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'color' => $color,
            'thresholds' => $thresholds
        ];
    }

    private function getDeviceStatus()
    {
        // Get latest commands
        $oxygenatorCommand = $this->commandModel->getCurrentCommand('oxygenator', 'NODEMCU_AQUASENSE_001');
        $waterPumpCommand = $this->commandModel->getCurrentCommand('water_pump', 'NODEMCU_AQUASENSE_001');

        // Get latest device logs for triggered_by information
        $oxygenatorLog = $this->deviceLogModel->getCurrentState('oxygenator');
        $waterPumpLog = $this->deviceLogModel->getCurrentState('water_pump');

        return [
            'oxygenator' => [
                'state' => ($oxygenatorCommand && strtoupper($oxygenatorCommand['command']) === 'ON') ? 'ON' : 'OFF',
                'last_updated' => $oxygenatorCommand ? $oxygenatorCommand['created_at'] : null,
                'triggered_by' => $oxygenatorLog ? $oxygenatorLog['triggered_by'] : 'unknown'
            ],
            'water_pump' => [
                'state' => ($waterPumpCommand && strtoupper($waterPumpCommand['command']) === 'ON') ? 'ON' : 'OFF',
                'last_updated' => $waterPumpCommand ? $waterPumpCommand['created_at'] : null,
                'triggered_by' => $waterPumpLog ? $waterPumpLog['triggered_by'] : 'unknown'
            ]
        ];
    }

    private function calculateStartDate($range)
    {
        $now = new \DateTime();
        
        switch ($range) {
            case '48h':
                $interval = 'P2D'; // 2 days
                break;
            case '72h':
                $interval = 'P3D'; // 3 days
                break;
            case '7d':
                $interval = 'P7D'; // 7 days
                break;
            case '14d':
                $interval = 'P14D'; // 14 days
                break;
            case '30d':
                $interval = 'P30D'; // 30 days
                break;
            case '24h':
            default:
                $interval = 'P1D'; // 1 day
                break;
        }
        
        $now->sub(new \DateInterval($interval));
        return $now->format('Y-m-d H:i:s');
    }

    private function formatLabel($timestamp, $range)
    {
        $date = new \DateTime($timestamp);
        
        switch ($range) {
            case '24h':
            case '48h':
            case '72h':
                return $date->format('H:i'); // Hours:Minutes
            case '7d':
                return $date->format('M d H:i'); // Month Day Hours:Minutes
            case '14d':
            case '30d':
                return $date->format('M d'); // Month Day
            default:
                return $date->format('H:i');
        }
    }


    public function testAjax()
    {
        // Simple test that always works
        return $this->response->setJSON([
            'success' => true,
            'message' => 'AJAX test successful!',
            'time' => date('Y-m-d H:i:s')
        ]);
    }

    public function test()
    {
        echo 'OK';
        exit;
    }
}