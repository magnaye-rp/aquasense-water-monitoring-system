<?php

use App\Controllers\ApiController;
use App\Controllers\DashboardController;
use App\Controllers\AuthController;

$routes->get('/', function() {
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

// API routes for NodeMCU/ESP32
$routes->group('api', function($routes) {
    $routes->post('receive-data', [ApiController::class, 'receiveData']);
    $routes->get('get-commands', [ApiController::class, 'getCommands']);
    $routes->post('control-device', [ApiController::class, 'controlDevice']);
    $routes->get('current-readings', [ApiController::class, 'getCurrentReadings']);
    $routes->get('historical-data', [ApiController::class, 'getHistoricalData']);
});

// Dashboard routes (protected)
$routes->group('dashboard', ['filter' => 'session'], function($routes) {
    $routes->get('/', [DashboardController::class, 'index']);
    $routes->get('sensor-data', [DashboardController::class, 'sensorData']);
    $routes->get('alerts', [DashboardController::class, 'alerts']);
    $routes->get('devices', [DashboardController::class, 'devices']);
    $routes->get('settings', [DashboardController::class, 'settings']);
    
    $routes->post('update-settings', [DashboardController::class, 'updateSettings']);
    $routes->post('control-device', [DashboardController::class, 'controlDevice']);
    $routes->get('get-current-data', [DashboardController::class, 'getCurrentData']);
    $routes->get('delete-alert/(:num)', [DashboardController::class, 'deleteAlert']);
});