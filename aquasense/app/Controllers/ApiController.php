<?php

namespace App\Controllers;

use App\Models\SensorReadingModel;
use App\Models\AlertModel;
use App\Models\DeviceLogModel;
use App\Models\SystemSettingsModel;
use CodeIgniter\API\ResponseTrait;

class ApiController extends BaseController
{
    use ResponseTrait;

    protected $sensorReadingModel;
    protected $alertModel;
    protected $deviceLogModel;
    protected $systemSettingsModel;

    // API Configuration
    protected $apiKeys = [
        'd484feef4f6e564920fabd0de3c58d77' => 'NODEMCU_WATER_001'
    ];

    public function __construct()
    {
        $this->sensorReadingModel = new SensorReadingModel();
        $this->alertModel = new AlertModel();
        $this->deviceLogModel = new DeviceLogModel();
        $this->systemSettingsModel = new SystemSettingsModel();
    }

    /**
     * Receive sensor data from ESP device
     */
    public function receiveData()
    {
        // Get API key from header or POST data
        $apiKey = $this->request->getHeaderLine('X-API-Key') ?? 
                 $this->request->getPost('api_key');

        // Validate API key
        if (!$this->validateApiKey($apiKey)) {
            return $this->failUnauthorized('Invalid API key');
        }

        // Get sensor data
        $temperature = $this->request->getPost('temperature');
        $turbidity = $this->request->getPost('turbidity');
        $ph = $this->request->getPost('ph');
        $autoMode = $this->request->getPost('auto_mode');

        // Validate required data
        if ($temperature === null || $turbidity === null || $ph === null) {
            return $this->fail('Missing sensor data', 400);
        }

        // Save sensor reading
        $sensorData = [
            'temperature' => (float)$temperature,
            'ph_level' => (float)$ph,
            'turbidity' => (float)$turbidity,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->sensorReadingModel->insert($sensorData);
        } catch (\Exception $e) {
            return $this->fail('Failed to save sensor data: ' . $e->getMessage(), 500);
        }

        // Get current system settings and thresholds
        $settings = $this->systemSettingsModel->first();
        $thresholds = $this->systemSettingsModel->getThresholds();

        // Check for alerts
        $this->checkForAlerts($sensorData, $thresholds);

        // Auto-control devices if in auto mode
        if ($autoMode == '1' || $autoMode == 1) {
            $this->autoControlDevices($sensorData, $settings);
        }

        // Get commands for the device
        $commands = $this->getDeviceCommands();

        return $this->respond([
            'status' => 'success',
            'message' => 'Data received successfully',
            'timestamp' => date('Y-m-d H:i:s'),
            'commands' => $commands
        ]);
    }

    /**
     * Get device commands (for NodeMCU to control relays)
     */
    public function getCommands()
    {
        $apiKey = $this->request->getHeaderLine('X-API-Key') ?? 
                 $this->request->getGet('api_key');

        if (!$this->validateApiKey($apiKey)) {
            return $this->failUnauthorized('Invalid API key');
        }

        $commands = $this->getDeviceCommands();

        return $this->respond([
            'status' => 'success',
            'commands' => $commands,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Manual control of devices via API
     */
    public function controlDevice()
    {
        // This endpoint requires authentication
        if (!auth()->loggedIn()) {
            return $this->failUnauthorized('Authentication required');
        }

        $device = $this->request->getPost('device');
        $action = $this->request->getPost('action');
        $duration = $this->request->getPost('duration'); // in seconds

        if (!in_array($device, ['oxygenator', 'water_pump'])) {
            return $this->fail('Invalid device', 400);
        }

        if (!in_array(strtolower($action), ['on', 'off'])) {
            return $this->fail('Invalid action', 400);
        }

        // Log the manual control
        $this->deviceLogModel->logDeviceAction($device, $action, 'manual');

        // Return command for device
        return $this->respond([
            'status' => 'success',
            'message' => "Device {$device} set to {$action}",
            'command' => [
                'device' => $device,
                'action' => $action,
                'duration' => $duration ?? 0
            ]
        ]);
    }

    /**
     * Get current sensor readings
     */
    public function getCurrentReadings()
    {
        $apiKey = $this->request->getHeaderLine('X-API-Key') ?? 
                 $this->request->getGet('api_key');

        if (!$this->validateApiKey($apiKey)) {
            return $this->failUnauthorized('Invalid API key');
        }

        $latest = $this->sensorReadingModel->getLatestReadings(1);
        $current = !empty($latest) ? $latest[0] : null;

        return $this->respond([
            'status' => 'success',
            'data' => $current,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get historical data
     */
    public function getHistoricalData()
    {
        $apiKey = $this->request->getHeaderLine('X-API-Key') ?? 
                 $this->request->getGet('api_key');

        if (!$this->validateApiKey($apiKey)) {
            return $this->failUnauthorized('Invalid API key');
        }

        $hours = $this->request->getGet('hours') ?? 24;
        $limit = $this->request->getGet('limit') ?? 100;

        $readings = $this->sensorReadingModel
            ->where("created_at >= DATE_SUB(NOW(), INTERVAL {$hours} HOUR)")
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        return $this->respond([
            'status' => 'success',
            'count' => count($readings),
            'data' => $readings
        ]);
    }

    /**
     * Private helper methods
     */
    private function validateApiKey($apiKey)
    {
        return isset($this->apiKeys[$apiKey]);
    }

    private function checkForAlerts($sensorData, $thresholds)
    {
        $alerts = [];

        // Check temperature
        if ($sensorData['temperature'] < $thresholds['temp_min']) {
            $alerts[] = [
                'type' => 'temperature',
                'message' => "Low temperature: {$sensorData['temperature']}°C (min: {$thresholds['temp_min']}°C)",
                'level' => 'warning'
            ];
        } elseif ($sensorData['temperature'] > $thresholds['temp_max']) {
            $alerts[] = [
                'type' => 'temperature',
                'message' => "High temperature: {$sensorData['temperature']}°C (max: {$thresholds['temp_max']}°C)",
                'level' => 'danger'
            ];
        }

        // Check pH
        if ($sensorData['ph_level'] < $thresholds['ph_min']) {
            $alerts[] = [
                'type' => 'ph',
                'message' => "Low pH level: {$sensorData['ph_level']} (min: {$thresholds['ph_min']})",
                'level' => 'danger'
            ];
        } elseif ($sensorData['ph_level'] > $thresholds['ph_max']) {
            $alerts[] = [
                'type' => 'ph',
                'message' => "High pH level: {$sensorData['ph_level']} (max: {$thresholds['ph_max']})",
                'level' => 'danger'
            ];
        }

        // Check turbidity
        if ($sensorData['turbidity'] > $thresholds['turbidity_max']) {
            $alerts[] = [
                'type' => 'turbidity',
                'message' => "High turbidity: {$sensorData['turbidity']} NTU (max: {$thresholds['turbidity_max']})",
                'level' => 'warning'
            ];
        }

        // Create alerts in database
        foreach ($alerts as $alert) {
            $this->alertModel->createAlert($alert['type'], $alert['message'], $alert['level']);
        }
    }

    private function autoControlDevices($sensorData, $settings)
    {
        if (!$settings) {
            return;
        }

        // Check oxygenator auto mode
        if ($settings['oxygenator_auto'] == 1) {
            $tempRange = $this->systemSettingsModel->getTemperatureRange();
            
            // Turn on oxygenator if temperature is high
            if ($sensorData['temperature'] > $tempRange['max']) {
                $this->deviceLogModel->logDeviceAction('oxygenator', 'ON', 'auto');
            } else {
                $this->deviceLogModel->logDeviceAction('oxygenator', 'OFF', 'auto');
            }
        }

        // Check water pump auto mode
        if ($settings['pump_auto'] == 1) {
            // Turn on pump if turbidity is high or based on interval
            if ($sensorData['turbidity'] > $settings['turbidity_limit']) {
                $this->deviceLogModel->logDeviceAction('water_pump', 'ON', 'auto');
            } else {
                // Check interval-based activation
                $lastPumpOn = $this->deviceLogModel
                    ->where('device_name', 'water_pump')
                    ->where('action', 'ON')
                    ->orderBy('created_at', 'DESC')
                    ->first();
                
                if (!$lastPumpOn || 
                    (time() - strtotime($lastPumpOn['created_at'])) > ($settings['pump_interval'] * 60)) {
                    $this->deviceLogModel->logDeviceAction('water_pump', 'ON', 'auto');
                } else {
                    $this->deviceLogModel->logDeviceAction('water_pump', 'OFF', 'auto');
                }
            }
        }
    }

    private function getDeviceCommands()
    {
        // Get latest device states from logs
        $oxygenatorState = $this->deviceLogModel
            ->where('device_name', 'oxygenator')
            ->orderBy('created_at', 'DESC')
            ->first();
        
        $waterPumpState = $this->deviceLogModel
            ->where('device_name', 'water_pump')
            ->orderBy('created_at', 'DESC')
            ->first();

        // Get system settings for mode
        $settings = $this->systemSettingsModel->first();

        return [
            'oxygenator' => $oxygenatorState && $oxygenatorState['action'] == 'ON' ? 1 : 0,
            'water_pump' => $waterPumpState && $waterPumpState['action'] == 'ON' ? 1 : 0,
            'mode' => $settings && $settings['oxygenator_auto'] == 1 ? 'auto' : 'manual'
        ];
    }
}