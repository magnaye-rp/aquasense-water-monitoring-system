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
        $this->session = \Config\Services::session();
        $this->commandModel = new \App\Models\DeviceCommandModel();
        $this->sensorReadingModel = new SensorReadingModel();
        $this->alertModel = new AlertModel();
        $this->deviceLogModel = new DeviceLogModel();
        $this->systemSettingsModel = new SystemSettingsModel();

        // Require authentication
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
    }

    public function index()
    {
        // Get latest sensor readings
        $latestReadings = $this->sensorReadingModel->getLatestReadings(1);
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
        $statistics = $this->sensorReadingModel->getStatistics('1 DAY');

        $data = [
            'title' => 'Dashboard - AquaSense',
            'currentReading' => $currentReading,
            'status' => $status,
            'alerts' => $alerts,
            'deviceStatus' => $deviceStatus,
            'chartData' => json_encode($chartData),
            'statistics' => $statistics,
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user()
        ];

        return view('dashboard/index', $data);
    }

    private function calculateReadingStatus($reading)
    {
        // Get thresholds from database
        $settingsModel = new SystemSettingsModel();
        $thresholds = $settingsModel->getThresholds();
        
        // Default thresholds if not found
        $tempMin = $thresholds['temp_min'] ?? 20;
        $tempMax = $thresholds['temp_max'] ?? 30;
        $phMin = $thresholds['ph_min'] ?? 6.5;
        $phMax = $thresholds['ph_max'] ?? 8.5;
        $turbidityMax = $thresholds['turbidity_max'] ?? 10;
        
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
        // Load helper
        helper('status');
        
        $sensorModel = new SensorReadingModel();
        $settingsModel = new SystemSettingsModel();
        
        // Get readings
        $readings = $sensorModel->orderBy('created_at', 'DESC')->findAll();
        
        // Calculate status for each reading
        $readingsWithStatus = [];
        foreach ($readings as $reading) {
            $reading['status'] = $this->calculateReadingStatus($reading);
            $readingsWithStatus[] = $reading;
        }
        
        // Get chart data
        $chartData = $this->getChartData();
        
        $data = [
            'readings' => $readingsWithStatus, // Now includes status
            'chartData' => $chartData,
            'thresholds' => $settingsModel->getThresholds()
        ];
        
        return view('dashboard/sensor-data', $data);
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

        $data = [
            'title' => 'Device Control - AquaSense',
            'deviceHistory' => $deviceHistory,
            'pager' => $pager,
            'currentStatus' => $currentStatus,
            'settings' => $settings,
            'unreadAlerts' => $this->alertModel->getUnreadCount(),
            'user' => auth()->user()
        ];

        return view('dashboard/devices', $data);
    }

    public function settings()
    {
        $settings = $this->systemSettingsModel->getCurrentSettings();
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

        if (!in_array($action, ['on', 'off'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }

        try {
            // Load model
            $commandModel = new \App\Models\DeviceCommandModel();

            // Add command to database
            $commandId = $commandModel->addCommand($device, strtoupper($action), 'NODEMCU_AQUASENSE_001');

            log_message('debug', "Command added to database. ID: {$commandId}");

            // Log device action
            $this->deviceLogModel->save([
                'device_name' => $device,
                'action' => strtoupper($action),
                'triggered_by' => 'manual',
                'user_id' => auth()->id()
            ]);

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
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $latestReadings = $this->sensorReadingModel->getLatestReadings(1);
        $currentReading = !empty($latestReadings) ? $latestReadings[0] : null;

        $status = $this->getSystemStatus($currentReading);
        $deviceStatus = $this->getDeviceStatus();
        $unreadAlerts = $this->alertModel->getUnreadCount();

        return $this->response->setJSON([
            'success' => true,
            'currentReading' => $currentReading,
            'status' => $status,
            'deviceStatus' => $deviceStatus,
            'unreadAlerts' => $unreadAlerts,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
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

        // Check thresholds
        if ($currentReading['temperature'] < $thresholds['temp_min'] || 
            $currentReading['temperature'] > $thresholds['temp_max']) {
            $status = 'warning';
            $message = 'Temperature out of range';
            $color = 'warning';
        }

        if ($currentReading['ph_level'] < $thresholds['ph_min'] || 
            $currentReading['ph_level'] > $thresholds['ph_max']) {
            $status = 'danger';
            $message = 'pH level critical';
            $color = 'danger';
        }

        if ($currentReading['turbidity'] > $thresholds['turbidity_max']) {
            $status = 'warning';
            $message = 'High turbidity detected';
            $color = 'warning';
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
        // Use the device command model instead of device logs
        $commandModel = new \App\Models\DeviceCommandModel();

        // Get latest commands
        $oxygenatorCommand = $commandModel->getCurrentCommand('oxygenator', 'NODEMCU_AQUASENSE_001');
        $waterPumpCommand = $commandModel->getCurrentCommand('water_pump', 'NODEMCU_AQUASENSE_001');

        // Log for debugging
        log_message('debug', 'Oxygenator command: ' . print_r($oxygenatorCommand, true));
        log_message('debug', 'Water pump command: ' . print_r($waterPumpCommand, true));

        return [
            'oxygenator' => [
                'state' => ($oxygenatorCommand && strtoupper($oxygenatorCommand['command']) === 'ON') ? 'ON' : 'OFF',
                'last_updated' => $oxygenatorCommand ? $oxygenatorCommand['created_at'] : null,
                'triggered_by' => 'manual'
            ],
            'water_pump' => [
                'state' => ($waterPumpCommand && strtoupper($waterPumpCommand['command']) === 'ON') ? 'ON' : 'OFF',
                'last_updated' => $waterPumpCommand ? $waterPumpCommand['created_at'] : null,
                'triggered_by' => 'manual'
            ]
        ];
    }

    private function getChartData()
    {
        $sensorModel = new SensorReadingModel();
        $settingsModel = new SystemSettingsModel();
        
        $readings = $sensorModel
            ->where("created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)")
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $chartData = [
            'labels' => [],
            'temperature' => [],
            'ph' => [],
            'turbidity' => [],
            'status' => []
        ];

        foreach ($readings as $reading) {
            $time = date('H:i', strtotime($reading['created_at']));
            $chartData['labels'][] = $time;
            $chartData['temperature'][] = $reading['temperature'];
            $chartData['ph'][] = $reading['ph_level'];
            $chartData['turbidity'][] = $reading['turbidity'];
            
            // Calculate status for chart data too
            $chartData['status'][] = $this->calculateReadingStatus($reading);
        }

        return $chartData;
    }

    // Temporary test method
    public function testAjax()
    {
        // Simple test that always works
        return $this->response->setJSON([
            'success' => true,
            'message' => 'AJAX test successful!',
            'time' => date('Y-m-d H:i:s')
        ]);
    }
}