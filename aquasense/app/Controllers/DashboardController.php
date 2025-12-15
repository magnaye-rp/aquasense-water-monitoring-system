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
        // Get latest sensor readings
        $latestReadings = $this->sensorReadingModel->getLatestReadings(1);
        $autoModeStatus = $this->systemSettingsModel->getAutoModeStatus();
        $currentReading = !empty($latestReadings) ? $latestReadings[0] : null;

        // Get system status
        $status = $this->getSystemStatus($currentReading);

        // Get recent alerts
        $alerts = $this->alertModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get device status
        $deviceStatus = $this->getDeviceStatus();

        // Get chart data
        $chartData = $this->getChartData();

        // Get statistics
        $statistics = $this->sensorReadingModel->getStatistics('1 DAY') ?? [
            'avg_temperature' => 0,
            'min_temperature' => 0,
            'max_temperature' => 0,
            'avg_ph' => 0,
            'min_ph' => 0,
            'max_ph' => 0,
            'avg_turbidity' => 0,
            'min_turbidity' => 0,
            'max_turbidity' => 0,
            'total_readings' => 0
        ];

        $data = [
            'title' => 'Dashboard - AquaSense',
            'currentReading' => $currentReading,
            'status' => $status,
            'alerts' => $alerts,
            'deviceStatus' => $deviceStatus,
            'chartData' => json_encode($chartData),
            'statistics' => $statistics,
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user(),
            'autoModeStatus' => $autoModeStatus,
            'latestCommands' => $this->commandModel->getLatestCommands('NODEMCU_AQUASENSE_001'),
        ];

        return view('dashboard/index', $data);
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
        $thresholds = $this->systemSettingsModel->getThresholds(); // Use existing instance
        
        // Get readings with pagination
        $perPage = 20;
        $readings = $this->sensorReadingModel->orderBy('created_at', 'DESC')->paginate($perPage);
        
        // Calculate status for each reading using helper
        $readingsWithStatus = [];
        foreach ($readings as $reading) {
            $reading['status'] = $this->calculateReadingStatus($reading); // Fixed method call
            $readingsWithStatus[] = $reading;
        }
        
        // Get chart data for last 24 hours
        $chartData = $this->getChartData();
        
        $data = [
            'title' => 'Sensor Data - AquaSense', // Added title
            'readings' => $readingsWithStatus,
            'chartData' => json_encode($chartData), 
            'thresholds' => $thresholds,
            'pager' => $this->sensorReadingModel->pager,
            'unreadAlerts' => $this->alertModel->getUnreadCount(), // Added
            'user' => auth()->user() // Added
        ];
        
        return view('dashboard/sensor-data', $data);
    }

    private function getChartData()
    {
        // Get last 24 hours of data
        $startTime = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $chartReadings = $this->sensorReadingModel
            ->where('created_at >=', $startTime)
            ->orderBy('created_at', 'ASC')
            ->findAll();
        
        $data = [
            'labels' => [],
            'temperature' => [],
            'ph' => [],
            'turbidity' => []
        ];
        
        foreach ($chartReadings as $reading) {
            $data['labels'][] = date('H:i', strtotime($reading['created_at']));
            $data['temperature'][] = floatval($reading['temperature']);
            $data['ph'][] = floatval($reading['ph_level']);
            $data['turbidity'][] = floatval($reading['turbidity']);
        }
        
        return $data;
    }

    public function alerts()
    {
        $alerts = $this->alertModel
            ->orderBy('created_at', 'DESC')
            ->paginate(20);

        $pager = $this->alertModel->pager;

        // Mark alerts as read when viewing
        $this->alertModel->markAllAsRead();

        $data = [
            'title' => 'Alerts - AquaSense',
            'alerts' => $alerts,
            'pager' => $pager,
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user()
        ];

        return view('dashboard/alerts', $data);
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
                'temperature_range' => '20-30'
            ];
            
            $this->systemSettingsModel->insert($defaultSettings);
            $settings = $this->systemSettingsModel->getCurrentSettings();
        }

        $waterTypes = [
            'freshwater' => 'Freshwater',
            'saltwater' => 'Saltwater',
            'generic' => 'Generic'
        ];

        $data = [
            'title' => 'Settings - AquaSense',
            'settings' => $settings,
            'waterTypes' => $waterTypes,
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user()
        ];

        return view('dashboard/settings', $data);
    }

    public function updateSettings()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $postData = $this->request->getPost();

        // Validate data
        $validation = \Config\Services::validation();
        $validation->setRules([
            'water_type' => 'required|in_list[freshwater,saltwater,generic]',
            'ph_good_min' => 'required|decimal',
            'ph_good_max' => 'required|decimal',
            'turbidity_limit' => 'required|integer',
            'temperature_range' => 'required|string'
        ]);

        if (!$validation->run($postData)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        // Parse boolean values
        $postData['oxygenator_auto'] = isset($postData['oxygenator_auto']) ? 1 : 0;
        $postData['pump_auto'] = isset($postData['pump_auto']) ? 1 : 0;
        
        // Convert interval values to integers
        $postData['oxygenator_interval'] = isset($postData['oxygenator_interval']) ? (int)$postData['oxygenator_interval'] : 0;
        $postData['pump_interval'] = isset($postData['pump_interval']) ? (int)$postData['pump_interval'] : 0;

        // Update settings
        $result = $this->systemSettingsModel->updateSettings($postData);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update settings'
        ]);
    }

    public function controlDevice()
    {
        // Get POST data
        $device = $this->request->getPost('device');
        $action = $this->request->getPost('action');

        // Log for debugging
        log_message('debug', 'Control device request: ' . print_r($this->request->getPost(), true));

        // Simple validation
        if (!$device || !$action) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing device or action parameters'
            ]);
        }

        if (!in_array($device, ['oxygenator', 'water_pump'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid device name'
            ]);
        }

        if (!in_array(strtolower($action), ['on', 'off'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }

        try {
            // Check for duplicate command (prevent multiple commands in short time)
            if ($this->commandModel->isDuplicateCommand($device, $action, 'NODEMCU_AQUASENSE_001')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'A similar command was recently sent. Please wait.'
                ]);
            }

            // Add command to database
            $commandId = $this->commandModel->addCommand($device, strtoupper($action), 'NODEMCU_AQUASENSE_001');

            log_message('debug', "Command added to database. ID: {$commandId}");

            // Log device action (FIXED: device_logs table doesn't have user_id column)
            $this->deviceLogModel->logDeviceAction(
                $device, 
                strtoupper($action), 
                'manual'
            );

            // Return success response
            return $this->response->setJSON([
                'success' => true,
                'message' => ucfirst($device) . ' turned ' . strtoupper($action) . ' successfully',
                'command_id' => $commandId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Failed to save command: ' . $e->getMessage());

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

    public function deleteAlert($id)
    {
        if ($this->alertModel->delete($id)) {
            return redirect()->to('/dashboard/alerts')->with('success', 'Alert deleted successfully');
        }

        return redirect()->to('/dashboard/alerts')->with('error', 'Failed to delete alert');
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