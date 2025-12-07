<?php

namespace App\Controllers;

use App\Models\SensorReadingModel;
use App\Models\AlertModel;
use App\Models\DeviceLogModel;
use App\Models\SystemSettingsModel;

class DashboardController extends BaseController
{
    protected $sensorReadingModel;
    protected $alertModel;
    protected $deviceLogModel;
    protected $systemSettingsModel;

    public function __construct()
    {
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

    public function sensorData()
    {
        $period = $this->request->getGet('period') ?? '24h';
        $periodMap = [
            '24h' => '1 DAY',
            '7d' => '7 DAY',
            '30d' => '30 DAY'
        ];
        
        $sqlPeriod = $periodMap[$period] ?? '1 DAY';
        $readings = $this->sensorReadingModel
            ->where("created_at >= DATE_SUB(NOW(), INTERVAL {$sqlPeriod})")
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Sensor Data - AquaSense',
            'readings' => $readings,
            'period' => $period,
            'user' => auth()->user()
        ];

        return view('dashboard/sensor_data', $data);
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
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $device = $this->request->getPost('device');
        $action = $this->request->getPost('action');

        if (!in_array($device, ['oxygenator', 'water_pump'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid device'
            ]);
        }

        if (!in_array(strtolower($action), ['on', 'off'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }

        // Log the action
        $this->deviceLogModel->logDeviceAction($device, $action, 'manual');

        return $this->response->setJSON([
            'success' => true,
            'message' => "Device {$device} turned {$action}",
            'action' => $action
        ]);
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
        $oxygenator = $this->deviceLogModel->getCurrentState('oxygenator');
        $waterPump = $this->deviceLogModel->getCurrentState('water_pump');
        
        return [
            'oxygenator' => [
                'state' => $oxygenator ? $oxygenator['action'] : 'OFF',
                'last_updated' => $oxygenator ? $oxygenator['created_at'] : null,
                'triggered_by' => $oxygenator ? $oxygenator['triggered_by'] : null
            ],
            'water_pump' => [
                'state' => $waterPump ? $waterPump['action'] : 'OFF',
                'last_updated' => $waterPump ? $waterPump['created_at'] : null,
                'triggered_by' => $waterPump ? $waterPump['triggered_by'] : null
            ]
        ];
    }

    private function getChartData()
    {
        $readings = $this->sensorReadingModel
            ->where("created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)")
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $chartData = [
            'labels' => [],
            'temperature' => [],
            'ph' => [],
            'turbidity' => []
        ];

        foreach ($readings as $reading) {
            $time = date('H:i', strtotime($reading['created_at']));
            $chartData['labels'][] = $time;
            $chartData['temperature'][] = $reading['temperature'];
            $chartData['ph'][] = $reading['ph_level'];
            $chartData['turbidity'][] = $reading['turbidity'];
        }

        return $chartData;
    }
}