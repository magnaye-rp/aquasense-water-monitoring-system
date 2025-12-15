<?php

namespace App\Controllers;

use App\Models\SensorReadingModel;
use App\Models\AlertModel;
use App\Models\DeviceLogModel;
use App\Models\SystemSettingsModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\DeviceCommandModel;
use App\Services\AutoModeService;

class ApiController extends BaseController
{
    use ResponseTrait;

    protected $sensorReadingModel;
    protected $alertModel;
    protected $deviceLogModel;
    protected $systemSettingsModel;
    protected $deviceCommandModel;
    protected $autoModeService;

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
        $this->autoModeService = new AutoModeService();
    }


    public function receiveData()
    {
        $apiKey = $this->request->getHeaderLine('X-API-Key') ?? 
                $this->request->getPost('api_key');

        $commandCheck = $this->request->getPost('command_check') ?? 0;

        if ($commandCheck == 1) {
            // Just check for commands, don't save sensor data
            log_message('debug', 'Command check request from device');
            // Return pending commands only
        } else {
            // Full sensor data - save to database
            $this->sensorReadingModel->insert($sensorData);
            // Check for alerts
            // Process auto mode
        }

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
        $deviceId = $this->request->getPost('device_id') ?? $this->apiKeys[$apiKey] ?? 'unknown';
        $oxygenatorState = $this->request->getPost('oxygenator_state') ?? 0;
        $waterPumpState = $this->request->getPost('water_pump_state') ?? 0;

        log_message('debug', "Device ID: {$deviceId}");
        log_message('debug', "Sensor Data - Temp: {$temperature}, Turb: {$turbidity}, pH: {$ph}");
        log_message('debug', "Current States - Oxygenator: {$oxygenatorState}, Water Pump: {$waterPumpState}");
        log_message('debug', "Auto Mode: {$autoMode}");

        // Prepare sensor data array
        $sensorData = [
            'temperature' => (float)$temperature,
            'ph_level' => (float)$ph,
            'turbidity' => (float)$turbidity,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Save sensor reading
            $this->sensorReadingModel->insert($sensorData);
            log_message('debug', '✅ Sensor data saved to database. ID: ' . $this->sensorReadingModel->getInsertID());

            // Check for alerts
            $thresholds = $this->systemSettingsModel->getThresholds();
            $this->alertModel->createSensorAlert($sensorData, $thresholds);
            
        } catch (\Exception $e) {
            log_message('error', '❌ Failed to save sensor data: ' . $e->getMessage());
        }

        // AUTO MODE PROCESSING
        $autoCommands = [];
        $autoModeEnabled = ($autoMode == '1' || $autoMode == 1);
        
        if ($autoModeEnabled) {
            try {
                log_message('debug', '🚀 Processing AUTO MODE...');
                $autoCommands = $this->autoModeService->processAutoMode($sensorData, $deviceId);
                log_message('debug', '✅ Auto mode processed. Commands generated: ' . count($autoCommands));
                
                // Log auto commands to device logs
                foreach ($autoCommands as $deviceName => $command) {
                    $this->deviceLogModel->logDeviceAction($deviceName, $command, 'auto');
                    log_message('debug', "📝 Logged auto command: {$deviceName} => {$command}");
                }
            } catch (\Exception $e) {
                log_message('error', '❌ Auto mode processing failed: ' . $e->getMessage());
            }
        }

        // Get pending commands (including auto-generated ones)
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
            'mode' => $autoModeEnabled ? 'auto' : 'manual'
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

        // Update current device states in database (if you have a devices table)
        $this->updateDeviceStates($deviceId, $oxygenatorState, $waterPumpState);

        log_message('debug', 'Final commands to send: ' . print_r($commands, true));
        log_message('debug', 'Auto commands generated: ' . print_r($autoCommands, true));
        log_message('debug', '=== API REQUEST END ===');

        return $this->respond([
            'status' => 'success',
            'message' => 'Data received successfully',
            'timestamp' => date('Y-m-d H:i:s'),
            'commands' => $commands,
            'auto_mode' => $autoModeEnabled ? 'enabled' : 'disabled',
            'auto_commands_generated' => $autoCommands,
            'pending_processed' => count($pendingCommands),
            'device_id' => $deviceId
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
     * Test fuzzy logic calculation
     */
    public function testFuzzyLogic()
    {
        if (!auth()->loggedIn()) {
            return $this->failUnauthorized('Authentication required');
        }

        $temp = $this->request->getPost('temperature') ?? 25.0;
        $ph = $this->request->getPost('ph') ?? 7.0;
        $turbidity = $this->request->getPost('turbidity') ?? 30.0;

        $sensorData = [
            'temperature' => (float)$temp,
            'ph_level' => (float)$ph,
            'turbidity' => (float)$turbidity
        ];

        try {
            // This would require exposing some protected methods or creating a test method
            $settings = $this->systemSettingsModel->getCurrentSettings();
            $thresholds = $this->systemSettingsModel->getThresholds();
            
            // Create a reflection to access protected methods (for testing)
            $reflection = new \ReflectionClass($this->autoModeService);
            
            $tempScoreMethod = $reflection->getMethod('calculateTemperatureScore');
            $tempScoreMethod->setAccessible(true);
            $tempScore = $tempScoreMethod->invoke($this->autoModeService, $temp, $thresholds);
            
            $phScoreMethod = $reflection->getMethod('calculatePhScore');
            $phScoreMethod->setAccessible(true);
            $phScore = $phScoreMethod->invoke($this->autoModeService, $ph, $thresholds);
            
            $turbScoreMethod = $reflection->getMethod('calculateTurbidityScore');
            $turbScoreMethod->setAccessible(true);
            $turbScore = $turbScoreMethod->invoke($this->autoModeService, $turbidity, $thresholds);
            
            // Evaluate what would happen
            $oxygenatorScore = ($tempScore * 0.6) + ($phScore * 0.4);
            $pumpScore = ($turbScore * 0.8);
            
            $oxygenatorCommand = $oxygenatorScore >= 70 ? 'ON' : ($oxygenatorScore <= 30 ? 'OFF' : 'NO_CHANGE');
            $pumpCommand = $pumpScore >= 65 ? 'ON' : ($pumpScore <= 35 ? 'OFF' : 'NO_CHANGE');

            return $this->respond([
                'status' => 'success',
                'test_data' => $sensorData,
                'thresholds' => $thresholds,
                'scores' => [
                    'temperature' => $tempScore,
                    'ph' => $phScore,
                    'turbidity' => $turbScore,
                    'oxygenator_total' => $oxygenatorScore,
                    'water_pump_total' => $pumpScore
                ],
                'recommendations' => [
                    'oxygenator' => $oxygenatorCommand,
                    'water_pump' => $pumpCommand
                ],
                'interpretation' => [
                    'oxygenator' => [
                        'ON' => 'Score >= 70',
                        'OFF' => 'Score <= 30',
                        'NO_CHANGE' => 'Score between 31-69'
                    ],
                    'water_pump' => [
                        'ON' => 'Score >= 65',
                        'OFF' => 'Score <= 35',
                        'NO_CHANGE' => 'Score between 36-64'
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->fail('Fuzzy logic test failed: ' . $e->getMessage(), 500);
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
                    
                case 'auto_mode':
                    // Test auto mode service
                    $sensorData = [
                        'temperature' => 28.5,
                        'ph_level' => 7.8,
                        'turbidity' => 75
                    ];
                    
                    $autoCommands = $this->autoModeService->processAutoMode($sensorData, 'NODEMCU_AQUASENSE_001');
                    
                    $response['success'] = true;
                    $response['message'] = 'Auto mode test complete';
                    $response['data'] = [
                        'test_sensor_data' => $sensorData,
                        'auto_commands_generated' => $autoCommands,
                        'service_working' => !empty($autoCommands) || true // Always true if no exception
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