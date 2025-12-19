<?php

namespace App\Controllers;

use App\Models\SensorReadingModel;
use App\Models\AlertModel;
use App\Models\DeviceLogModel;
use App\Models\SystemSettingsModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\DeviceCommandModel;

class ApiController extends BaseController
{
    use ResponseTrait;

    protected $sensorReadingModel;
    protected $alertModel;
    protected $deviceLogModel;
    protected $systemSettingsModel;
    protected $deviceCommandModel;

    // API Configuration
    protected $apiKeys = [
        'd484feef4f6e564920fabd0de3c58d77' => 'NODEMCU_AQUASENSE_001'
    ];

    public function __construct()
    {
        $this->helpers = array_merge($this->helpers, ['form', 'url']);
        $this->sensorReadingModel = new SensorReadingModel();
        $this->alertModel = new AlertModel();
        $this->deviceLogModel = new DeviceLogModel();
        $this->systemSettingsModel = new SystemSettingsModel();
        $this->deviceCommandModel = new DeviceCommandModel();
    }


    public function receiveData()
{
    // ===============================
    // API KEY VALIDATION
    // ===============================
    $apiKey = $this->request->getHeaderLine('X-API-Key')
           ?? $this->request->getPost('api_key');

    if (!$this->validateApiKey($apiKey)) {
        return $this->failUnauthorized('Invalid API key');
    }

    $deviceId = $this->apiKeys[$apiKey] ?? 'UNKNOWN_DEVICE';

    // ===============================
    // REQUEST DATA
    // ===============================
    $commandCheck = (int) ($this->request->getPost('command_check') ?? 0);

    $temperature = $this->request->getPost('temperature');
    $ph          = $this->request->getPost('ph');
    $turbidity   = $this->request->getPost('turbidity');

    $oxygenatorState = (int) ($this->request->getPost('oxygenator_state') ?? 0);
    $waterPumpState  = (int) ($this->request->getPost('water_pump_state') ?? 0);

    log_message('debug', "📡 Device: {$deviceId} | CommandCheck: {$commandCheck}");

    // ===============================
    // SAVE SENSOR DATA (IF NOT COMMAND CHECK)
    // ===============================
    if ($commandCheck !== 1) {
        try {
            $sensorData = [
                'temperature' => (float) $temperature,
                'ph_level'    => (float) $ph,
                'turbidity'   => (float) $turbidity,
                'created_at'  => date('Y-m-d H:i:s')
            ];

            $this->sensorReadingModel->insert($sensorData);

            // Alerts (threshold-based)
            $thresholds = $this->systemSettingsModel->getThresholds();
            $this->alertModel->createSensorAlert($sensorData, $thresholds);

            log_message('debug', '✅ Sensor data saved');

        } catch (\Exception $e) {
            log_message('error', '❌ Sensor save failed: ' . $e->getMessage());
        }
    }

    // ===============================
    // DEFAULT RESPONSE COMMANDS
    // ===============================
    $commands = [
        'oxygenator' => 0,
        'water_pump' => 0,
        'mode'       => 'manual'
    ];

    // ===============================
    // GET PENDING COMMANDS
    // ===============================
    try {
        $pendingCommands = $this->deviceCommandModel->getPendingCommands($deviceId);
    } catch (\Exception $e) {
        log_message('error', '❌ Fetch commands failed: ' . $e->getMessage());
        $pendingCommands = [];
    }

    // ===============================
    // APPLY PENDING COMMANDS
    // ===============================
    foreach ($pendingCommands as $cmd) {
        $device  = $cmd['device_name'] ?? '';
        $command = strtoupper($cmd['command'] ?? '');

        if ($device === 'oxygenator') {
            $commands['oxygenator'] = ($command === 'ON') ? 1 : 0;
        }

        if ($device === 'water_pump') {
            $commands['water_pump'] = ($command === 'ON') ? 1 : 0;
        }

        // Mark command as executed
        try {
            $this->deviceCommandModel->markExecuted($cmd['id']);
        } catch (\Exception $e) {
            log_message('error', '❌ Command mark failed: ' . $e->getMessage());
        }
    }

    // ===============================
    // FALLBACK TO LAST KNOWN STATE
    // ===============================
    if (empty($pendingCommands)) {
        $lastOxy  = $this->deviceCommandModel->getCurrentCommand('oxygenator', $deviceId);
        $lastPump = $this->deviceCommandModel->getCurrentCommand('water_pump', $deviceId);

        if ($lastOxy) {
            $commands['oxygenator'] = strtoupper($lastOxy['command']) === 'ON' ? 1 : 0;
        }

        if ($lastPump) {
            $commands['water_pump'] = strtoupper($lastPump['command']) === 'ON' ? 1 : 0;
        }
    }

    // ===============================
    // UPDATE DEVICE HEARTBEAT
    // ===============================
    $this->updateDeviceStates($deviceId, $oxygenatorState, $waterPumpState);

    log_message('debug', '➡️ Commands sent: ' . json_encode($commands));

    // ===============================
    // JSON RESPONSE (IMPORTANT)
    // ===============================
    return $this->respond([
        'status'     => 'success',
        'device_id'  => $deviceId,
        'commands'   => $commands,
        'timestamp'  => date('Y-m-d H:i:s')
    ]);
}


    public function getCommands()
    {
        $apiKey = $this->request->getHeaderLine('X-API-Key') ?? 
                $this->request->getGet('api_key');

        if (!$this->validateApiKey($apiKey)) {
            return $this->failUnauthorized('Invalid API key');
        }

        $deviceId = $this->apiKeys[$apiKey];
        
        $commands = $this->deviceCommandModel->getLatestCommandsForDevice($deviceId);

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

        $apiKey = $this->request->getHeaderLine('X-API-Key');
        $isAuthenticated = auth()->loggedIn();
        $hasValidApiKey = $this->validateApiKey($apiKey);
        
        if (!$isAuthenticated && !$hasValidApiKey) {
            return $this->failUnauthorized('Authentication or valid API key required');
        }
    

        $device = $this->request->getPost('device');
        $action = $this->request->getPost('action');
        $deviceId = $this->request->getPost('device_id') ?? 'NODEMCU_AQUASENSE_001';
        $duration = $this->request->getPost('duration'); // in seconds

        if (!in_array($device, ['oxygenator', 'water_pump'])) {
            return $this->fail('Invalid device', 400);
        }

        if (!in_array(strtolower($action), ['on', 'off'])) {
            return $this->fail('Invalid action', 400);
        }

        try {
            // Add command to database
            $commandId = $this->deviceCommandModel->addCommand($device, strtoupper($action), $deviceId);
            
            // Log the manual control
            $this->deviceLogModel->logDeviceAction($device, $action, 'manual');

            return $this->respond([
                'status' => 'success',
                'message' => "Device {$device} set to {$action}",
                'command_id' => $commandId,
                'command' => [
                    'device' => $device,
                    'action' => $action,
                    'duration' => $duration ?? 0,
                    'device_id' => $deviceId
                ]
            ]);
        } catch (\Exception $e) {
            return $this->fail('Failed to save command: ' . $e->getMessage(), 500);
        }
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
     * Get auto mode status and thresholds
     */
    public function getAutoModeStatus()
    {
        if (!auth()->loggedIn()) {
            return $this->failUnauthorized('Authentication required');
        }

        try {
            $settings = $this->systemSettingsModel->getCurrentSettings();
            $thresholds = $this->systemSettingsModel->getThresholds();
            
            // Simulate auto mode decision with current sensor data
            $latestReading = $this->sensorReadingModel->getLatestReadings(1);
            $currentReading = !empty($latestReading) ? $latestReading[0] : null;
            
            $autoCommands = [];
            if ($currentReading) {
                $autoCommands = $this->autoModeService->evaluateAutoCommands($currentReading, 'NODEMCU_AQUASENSE_001');
            }

            return $this->respond([
                'status' => 'success',
                'auto_mode' => [
                    'oxygenator_auto' => (bool)($settings['oxygenator_auto'] ?? 0),
                    'pump_auto' => (bool)($settings['pump_auto'] ?? 0),
                    'oxygenator_interval' => (int)($settings['oxygenator_interval'] ?? 0),
                    'pump_interval' => (int)($settings['pump_interval'] ?? 0)
                ],
                'thresholds' => $thresholds,
                'current_reading' => $currentReading,
                'suggested_commands' => $autoCommands,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return $this->fail('Failed to get auto mode status: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Private helper methods
     */
    private function validateApiKey($apiKey)
    {
        return isset($this->apiKeys[$apiKey]);
    }

    private function updateDeviceStates($deviceId, $oxygenatorState, $waterPumpState)
    {
        try {
            // Check if you have a DeviceModel
            if (class_exists('\App\Models\DeviceModel')) {
                $deviceModel = new \App\Models\DeviceModel();
                
                // Update oxygenator status
                $oxygenator = $deviceModel->where('name', 'oxygenator')->first();
                if ($oxygenator) {
                    $deviceModel->update($oxygenator['id'], [
                        'status' => 'online',
                        'last_seen' => date('Y-m-d H:i:s')
                    ]);
                }
                
                // Update pump status
                $pump = $deviceModel->where('name', 'water_pump')->first();
                if ($pump) {
                    $deviceModel->update($pump['id'], [
                        'status' => 'online',
                        'last_seen' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        } catch (\Exception $e) {
            log_message('debug', 'Note: Could not update device states - DeviceModel might not exist: ' . $e->getMessage());
        }
    }

    
    public function testCommand()
    {
        // Get command ID from query string
        $commandId = $this->request->getGet('id');
        
        $db = \Config\Database::connect();
        
        $command = null;
        $pending = [];
        
        try {
            // Get specific command if ID provided
            if ($commandId && is_numeric($commandId)) {
                $command = $db->table('device_commands')
                             ->where('id', $commandId)
                             ->get()
                             ->getRowArray();
            }
            
            // Get all pending commands
            $pending = $db->table('device_commands')
                         ->where('status', 'pending')
                         ->orderBy('created_at', 'DESC')
                         ->get()
                         ->getResultArray();
            
            return $this->respond([
                'success' => true,
                'requested_id' => $commandId,
                'command_found' => $command ? true : false,
                'command' => $command,
                'all_pending' => $pending,
                'pending_count' => count($pending),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'requested_id' => $commandId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }
}