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
        $apiKey = $this->request->getHeaderLine('X-API-Key') ?? 
                $this->request->getPost('api_key');

        // Log for debugging
        log_message('debug', '=== API REQUEST START ===');
        log_message('debug', 'API Key: ' . ($apiKey ?? 'none'));

        // Validate API key
        if (!$this->validateApiKey($apiKey)) {
            log_message('debug', 'API Key validation failed');
            return $this->failUnauthorized('Invalid API key');
        }

        // Get sensor data
        $temperature = $this->request->getPost('temperature');
        $turbidity = $this->request->getPost('turbidity');
        $ph = $this->request->getPost('ph');
        $autoMode = $this->request->getPost('auto_mode');
        $deviceId = $this->request->getPost('device_id') ?? 'unknown';
        $oxygenatorState = $this->request->getPost('oxygenator_state') ?? 0;
        $waterPumpState = $this->request->getPost('water_pump_state') ?? 0;

        log_message('debug', "Device ID: {$deviceId}");
        log_message('debug', "Sensor Data - Temp: {$temperature}, Turb: {$turbidity}, pH: {$ph}");
        log_message('debug', "Current States - Oxygenator: {$oxygenatorState}, Water Pump: {$waterPumpState}");

        // Save sensor reading
        $sensorData = [
            'temperature' => (float)$temperature,
            'ph_level' => (float)$ph,
            'turbidity' => (float)$turbidity,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->sensorReadingModel->insert($sensorData);
            log_message('debug', '✅ Sensor data saved to database. ID: ' . $this->sensorReadingModel->getInsertID());
        } catch (\Exception $e) {
            log_message('error', '❌ Failed to save sensor data: ' . $e->getMessage());
        }

        // Get pending commands
        try {
            $pendingCommands = $this->deviceCommandModel->getPendingCommands($deviceId);
            log_message('debug', 'Pending commands found: ' . count($pendingCommands));
        } catch (\Exception $e) {
            log_message('error', 'Error getting pending commands: ' . $e->getMessage());
            $pendingCommands = [];
        }

        // Prepare response commands
        $commands = [
            'oxygenator' => 0,  // Default to OFF
            'water_pump' => 0,  // Default to OFF
            'mode' => 'auto'
        ];

        // Process each pending command
        foreach ($pendingCommands as $cmd) {
            $deviceName = $cmd['device_name'] ?? '';
            $command = strtoupper($cmd['command'] ?? '');
            
            log_message('debug', "Processing: {$deviceName} => {$command} (ID: {$cmd['id']})");
            
            if ($deviceName === 'oxygenator') {
                $commands['oxygenator'] = ($command === 'ON') ? 1 : 0;
                log_message('debug', "Set oxygenator to: {$commands['oxygenator']}");
            }
            
            if ($deviceName === 'water_pump') {
                $commands['water_pump'] = ($command === 'ON') ? 1 : 0;
                log_message('debug', "Set water_pump to: {$commands['water_pump']}");
            }
            
            // Mark as executed
            try {
                $this->deviceCommandModel->markExecuted($cmd['id']);
                log_message('debug', "✅ Command {$cmd['id']} marked as executed");
            } catch (\Exception $e) {
                log_message('error', "Failed to mark command as executed: " . $e->getMessage());
            }
        }

        // If no pending commands, get latest state from database
        if (empty($pendingCommands)) {
            try {
                $oxygenatorCommand = $this->deviceCommandModel->getCurrentCommand('oxygenator', $deviceId);
                $waterPumpCommand = $this->deviceCommandModel->getCurrentCommand('water_pump', $deviceId);
                
                if ($oxygenatorCommand) {
                    $commands['oxygenator'] = (strtoupper($oxygenatorCommand['command']) === 'ON') ? 1 : 0;
                    log_message('debug', "Using latest oxygenator command: {$commands['oxygenator']}");
                }
                
                if ($waterPumpCommand) {
                    $commands['water_pump'] = (strtoupper($waterPumpCommand['command']) === 'ON') ? 1 : 0;
                    log_message('debug', "Using latest water pump command: {$commands['water_pump']}");
                }
            } catch (\Exception $e) {
                log_message('error', 'Error getting latest commands: ' . $e->getMessage());
            }
        }

        log_message('debug', 'Final commands to send: ' . print_r($commands, true));
        log_message('debug', '=== API REQUEST END ===');

        return $this->respond([
            'status' => 'success',
            'message' => 'Data received successfully',
            'timestamp' => date('Y-m-d H:i:s'),
            'commands' => $commands,
            'pending_processed' => count($pendingCommands)
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

    public function test()
    {
        $testType = $this->request->getPost('test');
        
        $response = [
            'success' => false,
            'message' => '',
            'data' => []
        ];
        
        try {
            $db = \Config\Database::connect();
            
            switch ($testType) {
                case 'connection':
                    $response['success'] = (bool)$db->connID;
                    $response['message'] = $db->connID ? 'Database connected successfully' : 'Database connection failed';
                    $response['data'] = [
                        'connected' => (bool)$db->connID,
                        'database' => $db->database,
                        'hostname' => $db->hostname
                    ];
                    break;
                    
                case 'tables':
                    $tables = $db->listTables();
                    $response['success'] = in_array('device_commands', $tables);
                    $response['message'] = 'Found ' . count($tables) . ' tables';
                    $response['data'] = [
                        'total_tables' => count($tables),
                        'tables' => $tables,
                        'device_commands_exists' => in_array('device_commands', $tables)
                    ];
                    break;
                    
                case 'insert':
                    // Test insert
                    $result = $db->table('device_commands')->insert([
                        'device_name' => 'oxygenator',
                        'command' => 'ON',
                        'status' => 'pending',
                        'device_id' => 'TEST_' . time(),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    $response['success'] = (bool)$result;
                    $response['message'] = $result ? 'Test command inserted successfully' : 'Failed to insert command';
                    $response['data'] = [
                        'insert_success' => (bool)$result,
                        'insert_id' => $db->insertID(),
                        'test_id' => 'TEST_' . time()
                    ];
                    break;
                    
                case 'api':
                    // Simulate what NodeMCU would receive
                    $pending = $db->table('device_commands')
                                ->where('status', 'pending')
                                ->where('device_id', 'NODEMCU_AQUASENSE_001')
                                ->orWhere('device_id IS NULL')
                                ->get()
                                ->getResultArray();
                    
                    $commands = [
                        'oxygenator' => 0,
                        'water_pump' => 0,
                        'mode' => 'auto'
                    ];
                    
                    foreach ($pending as $cmd) {
                        if ($cmd['device_name'] === 'oxygenator') {
                            $commands['oxygenator'] = ($cmd['command'] === 'ON') ? 1 : 0;
                        }
                        if ($cmd['device_name'] === 'water_pump') {
                            $commands['water_pump'] = ($cmd['command'] === 'ON') ? 1 : 0;
                        }
                    }
                    
                    $response['success'] = true;
                    $response['message'] = 'API simulation complete';
                    $response['data'] = [
                        'pending_commands' => $pending,
                        'commands_to_send' => $commands,
                        'pending_count' => count($pending)
                    ];
                    break;
                    
                default:
                    $response['message'] = 'Invalid test type';
            }
            
        } catch (\Exception $e) {
            $response['message'] = 'Test failed: ' . $e->getMessage();
            $response['data']['error'] = $e->getMessage();
        }
        
        return $this->respond($response);
    }
    // Add this method to your existing ApiController.php
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