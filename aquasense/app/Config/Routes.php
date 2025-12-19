<?php

use App\Controllers\ApiController;
use App\Controllers\DashboardController;
use App\Controllers\AuthController;
use App\Controllers\TestController;

// Service routes for initial setup
$routes->get('init-settings', function() {
    $model = new \App\Models\SystemSettingsModel();
    $existing = $model->first();
    
    if (!$existing) {
        $model->insert([
            'water_type' => 'generic',
            'oxygenator_auto' => 1,  // Enable auto mode by default
            'pump_auto' => 1,
            'oxygenator_interval' => 30,  // 30 minutes
            'pump_interval' => 60,  // 60 minutes
            'ph_good_min' => 6.5,
            'ph_good_max' => 8.5,
            'turbidity_limit' => 100,
            'temperature_range' => '20-30'
        ]);
        return "Settings initialized successfully!";
    }
    return "Settings already exist in the database.";
});

// Home route - redirect based on authentication
$routes->get('/', function() {
    if (auth()->loggedIn()) {
        return redirect()->to('/dashboard');
    }
    return redirect()->to('/login');
});

// Authentication routes
$routes->get('/login', [AuthController::class, 'loginView']);
$routes->post('/login', [AuthController::class, 'login']);

$routes->get('/register', [AuthController::class, 'registerView']);
$routes->post('/register', [AuthController::class, 'register']);

$routes->get('/forgot-password', [AuthController::class, 'forgotPasswordView']);
$routes->post('/forgot-password', [AuthController::class, 'forgotPassword']);

$routes->get('/logout', [AuthController::class, 'logout']);

// API routes for NodeMCU/ESP32 - NO authentication required for device communication
$routes->group('api', function($routes) {
    // Main data receiving endpoint (from Arduino)
    $routes->post('receive-data', [ApiController::class, 'receiveData']);
    
    // Device command endpoints
    $routes->get('get-commands', [ApiController::class, 'getCommands']);
    $routes->post('control-device', [ApiController::class, 'controlDevice']);
    
    // Sensor data endpoints
    $routes->get('current-readings', [ApiController::class, 'getCurrentReadings']);
    $routes->get('historical-data', [ApiController::class, 'getHistoricalData']);
    
    // Auto mode endpoints
    $routes->get('auto-mode-status', [ApiController::class, 'getAutoModeStatus']);
    
    // Test endpoints
    $routes->post('test', [ApiController::class, 'test']);
    $routes->get('test-command', [ApiController::class, 'testCommand']);
    
    // Fuzzy logic test (protected)
    $routes->post('test-fuzzy-logic', [ApiController::class, 'testFuzzyLogic'], ['filter' => 'session']);
});

// Dashboard routes (protected - requires login)
$routes->group('dashboard', ['filter' => 'session'], function($routes) {
    // Main dashboard route - explicitly handle both with and without trailing slash
    $routes->get('/', [DashboardController::class, 'main']);
    $routes->get('main', [DashboardController::class, 'main']);
    
    // Prevent auto-routing to index() by explicitly redirecting
    // This ensures /dashboard always goes to main()
    $routes->get('sensor-data', [DashboardController::class, 'sensorData']);
    $routes->get('get-chart-data', [DashboardController::class, 'getChartData']);
    $routes->get('get-current-readings', [DashboardController::class, 'getCurrentReadings']);
    $routes->get('alerts', [DashboardController::class, 'alerts']);
    $routes->get('devices', [DashboardController::class, 'devices']);
    $routes->get('settings', [DashboardController::class, 'settings']);

    // Fix these routes to use DashboardController
    $routes->get('delete-alert/(:num)', [DashboardController::class, 'deleteAlert']);
    $routes->post('delete-old-alerts', [DashboardController::class, 'deleteOldAlerts']);
    $routes->post('clear-all-alerts', [DashboardController::class, 'clearAllAlerts']);
    $routes->post('toggle-auto-mode', 'DashboardController::toggleAutoMode');
    
    // AJAX endpoints for dashboard
    $routes->post('update-settings', [DashboardController::class, 'updateSettings']);
    $routes->post('control-device', [DashboardController::class, 'controlDevice']);
    $routes->post('toggle-auto-mode', [DashboardController::class, 'toggleAutoMode']);
    $routes->get('get-current-data', [DashboardController::class, 'getCurrentData']);
    
    // Test routes
    $routes->get('test-dashboard', [DashboardController::class, 'test']);
    $routes->get('test-ajax', [DashboardController::class, 'testAjax']);
});

// Test routes for debugging
$routes->group('test', function($routes) {
    $routes->get('/', [TestController::class, 'index']);
    $routes->get('db', [TestController::class, 'dbTest']);
    $routes->get('system', function() {
        echo "<pre>";
        
        // Check models
        $models = [
            'AlertModel' => new \App\Models\AlertModel(),
            'SystemSettingsModel' => new \App\Models\SystemSettingsModel(),
            'DeviceCommandModel' => new \App\Models\DeviceCommandModel(),
            'DeviceLogModel' => new \App\Models\DeviceLogModel(),
            'SensorReadingModel' => new \App\Models\SensorReadingModel(),
        ];
        
        foreach ($models as $name => $model) {
            echo "=== $name ===\n";
            echo "Table: " . $model->table . "\n";
            echo "Allowed Fields: " . implode(', ', $model->allowedFields) . "\n";
            echo "Record Count: " . $model->countAll() . "\n";
            echo "\n";
        }
        
        // Check AutoModeService
        if (class_exists('App\Services\AutoModeService')) {
            echo "AutoModeService: EXISTS\n";
        } else {
            echo "AutoModeService: NOT FOUND\n";
        }
        
        echo "</pre>";
    });
});

// Health check route for monitoring
$routes->get('health', function() {
    try {
        $db = \Config\Database::connect();
        $connected = (bool)$db->connID;
        
        return json_encode([
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'database' => $connected ? 'connected' : 'disconnected',
            'services' => [
                'api' => 'available',
                'dashboard' => 'available',
                'authentication' => 'available'
            ]
        ]);
    } catch (\Exception $e) {
        return json_encode([
            'status' => 'error',
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => $e->getMessage()
        ]);
    }
});

// Route for debugging API endpoints
$routes->get('api/debug', function() {
    echo "<h1>API Debug Information</h1>";
    echo "<h3>Available Endpoints:</h3>";
    echo "<ul>";
    echo "<li><strong>POST /api/receive-data</strong> - Receive sensor data from Arduino</li>";
    echo "<li><strong>GET /api/get-commands</strong> - Get pending commands for device</li>";
    echo "<li><strong>POST /api/control-device</strong> - Manual device control</li>";
    echo "<li><strong>GET /api/current-readings</strong> - Get latest sensor readings</li>";
    echo "<li><strong>GET /api/historical-data</strong> - Get historical sensor data</li>";
    echo "<li><strong>GET /api/auto-mode-status</strong> - Get auto mode settings</li>";
    echo "<li><strong>POST /api/test</strong> - Test various functions</li>";
    echo "<li><strong>GET /api/test-command</strong> - Test command retrieval</li>";
    echo "</ul>";
    
    echo "<h3>Required Headers for Arduino:</h3>";
    echo "<pre>";
    echo "POST /api/receive-data\n";
    echo "Content-Type: application/x-www-form-urlencoded\n";
    echo "X-API-Key: d484feef4f6e564920fabd0de3c58d77\n";
    echo "\n";
    echo "Parameters:\n";
    echo "temperature=25.5\n";
    echo "turbidity=45\n";
    echo "ph=7.2\n";
    echo "auto_mode=1\n";
    echo "device_id=NODEMCU_AQUASENSE_001\n";
    echo "oxygenator_state=0\n";
    echo "water_pump_state=0\n";
    echo "</pre>";
});