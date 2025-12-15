<?php
// app/Services/AutoModeService.php

namespace App\Services;

use App\Models\SensorReadingModel;
use App\Models\SystemSettingsModel;
use App\Models\DeviceCommandModel;
use App\Models\DeviceLogModel;

class AutoModeService
{
    protected $sensorReadingModel;
    protected $systemSettingsModel;
    protected $commandModel;
    protected $deviceLogModel;
    
    public function __construct()
    {
        $this->sensorReadingModel = new SensorReadingModel();
        $this->systemSettingsModel = new SystemSettingsModel();
        $this->commandModel = new DeviceCommandModel();
        $this->deviceLogModel = new DeviceLogModel();
    }
    
    /**
     * Evaluate if devices should be activated based on sensor data
     * 
     * @param array $sensorData Current sensor readings
     * @param string $deviceId Device ID
     * @return array Commands to execute
     */
    public function evaluateAutoCommands($sensorData, $deviceId = 'NODEMCU_AQUASENSE_001')
    {
        $settings = $this->systemSettingsModel->getCurrentSettings();
        
        if (!$settings) {
            return [];
        }
        
        $commands = [];
        
        // Check if auto mode is enabled for each device
        $oxygenatorAuto = (bool)$settings['oxygenator_auto'];
        $pumpAuto = (bool)$settings['pump_auto'];
        
        // Get thresholds
        $thresholds = $this->systemSettingsModel->getThresholds();
        
        // Evaluate oxygenator
        if ($oxygenatorAuto) {
            $oxygenatorCommand = $this->evaluateOxygenator($sensorData, $thresholds, $settings);
            if ($oxygenatorCommand !== null) {
                $commands['oxygenator'] = $oxygenatorCommand;
            }
        }
        
        // Evaluate water pump
        if ($pumpAuto) {
            $pumpCommand = $this->evaluateWaterPump($sensorData, $thresholds, $settings);
            if ($pumpCommand !== null) {
                $commands['water_pump'] = $pumpCommand;
            }
        }
        
        // Execute commands
        $executedCommands = [];
        foreach ($commands as $deviceName => $command) {
            // Check if we need to send this command (avoid duplicates)
            $currentCommand = $this->commandModel->getCurrentCommand($deviceName, $deviceId);
            
            if (!$currentCommand || $currentCommand['command'] !== $command) {
                // Add new command
                $commandId = $this->commandModel->addCommand(
                    $deviceName, 
                    $command, 
                    $deviceId
                );
                
                if ($commandId) {
                    // Log the auto action
                    $this->deviceLogModel->logDeviceAction(
                        $deviceName,
                        $command,
                        'auto'
                    );
                    
                    $executedCommands[$deviceName] = $command;
                }
            }
        }
        
        return $executedCommands;
    }
    
    /**
     * Fuzzy logic for oxygenator control
     * 
     * @param array $sensorData Sensor readings
     * @param array $thresholds System thresholds
     * @param array $settings System settings
     * @return string|null Command (ON/OFF) or null if no change needed
     */
    private function evaluateOxygenator($sensorData, $thresholds, $settings)
    {
        $temperature = $sensorData['temperature'] ?? 0;
        $ph = $sensorData['ph_level'] ?? 7.0;
        
        // Calculate activation score (0-100)
        $score = 0;
        
        // Temperature component (weight: 60%)
        $tempScore = $this->calculateTemperatureScore($temperature, $thresholds);
        $score += $tempScore * 0.6;
        
        // pH component (weight: 40%)
        $phScore = $this->calculatePhScore($ph, $thresholds);
        $score += $phScore * 0.4;
        
        // Check interval-based activation
        $intervalScore = $this->checkOxygenatorInterval($settings);
        $score += $intervalScore * 0.2; // 20% weight for interval
        
        // Determine action based on score
        if ($score >= 70) {
            return 'ON';
        } elseif ($score <= 30) {
            return 'OFF';
        }
        
        return null; // No change
    }
    
    /**
     * Fuzzy logic for water pump control
     * 
     * @param array $sensorData Sensor readings
     * @param array $thresholds System thresholds
     * @param array $settings System settings
     * @return string|null Command (ON/OFF) or null if no change needed
     */
    private function evaluateWaterPump($sensorData, $thresholds, $settings)
    {
        $turbidity = $sensorData['turbidity'] ?? 0;
        
        // Calculate activation score (0-100)
        $score = 0;
        
        // Turbidity component (weight: 80%)
        $turbidityScore = $this->calculateTurbidityScore($turbidity, $thresholds);
        $score += $turbidityScore * 0.8;
        
        // Check interval-based activation
        $intervalScore = $this->checkPumpInterval($settings);
        $score += $intervalScore * 0.2; // 20% weight for interval
        
        // Determine action based on score
        if ($score >= 65) {
            return 'ON';
        } elseif ($score <= 35) {
            return 'OFF';
        }
        
        return null; // No change
    }
    
    /**
     * Calculate temperature fuzzy score (0-100)
     * Higher score means oxygenator should be ON
     */
    private function calculateTemperatureScore($temperature, $thresholds)
    {
        $tempMin = $thresholds['temp_min'] ?? 20;
        $tempMax = $thresholds['temp_max'] ?? 30;
        $optimalMin = $tempMin + 2;
        $optimalMax = $tempMax - 2;
        
        // Fuzzy logic for temperature
        if ($temperature <= $optimalMin) {
            return 30; // Low temp, moderate need for oxygen
        } elseif ($temperature >= $optimalMax) {
            return 90; // High temp, high need for oxygen
        } elseif ($temperature > $optimalMin && $temperature < $optimalMax) {
            return 50; // Optimal temp, moderate oxygen
        }
        
        return 50;
    }
    
    /**
     * Calculate pH fuzzy score (0-100)
     * Higher score means oxygenator should be ON (oxygenation can affect pH)
     */
    private function calculatePhScore($ph, $thresholds)
    {
        $phMin = $thresholds['ph_min'] ?? 6.5;
        $phMax = $thresholds['ph_max'] ?? 8.5;
        $optimalMin = $phMin + 0.5;
        $optimalMax = $phMax - 0.5;
        
        // Fuzzy logic for pH
        if ($ph <= $optimalMin) {
            return 80; // Low pH, oxygenation can help raise pH
        } elseif ($ph >= $optimalMax) {
            return 20; // High pH, oxygenation might not help
        } elseif ($ph > $optimalMin && $ph < $optimalMax) {
            return 50; // Optimal pH
        }
        
        return 50;
    }
    
    /**
     * Calculate turbidity fuzzy score (0-100)
     * Higher score means pump should be ON
     */
    private function calculateTurbidityScore($turbidity, $thresholds)
    {
        $turbidityMax = $thresholds['turbidity_max'] ?? 100;
        $warningLevel = $turbidityMax * 0.7;
        
        // Fuzzy logic for turbidity
        if ($turbidity <= 20) {
            return 20; // Clear water, pump not needed
        } elseif ($turbidity >= $warningLevel) {
            return 90; // High turbidity, pump needed
        } elseif ($turbidity > 20 && $turbidity < $warningLevel) {
            // Linear increase
            return 20 + (($turbidity - 20) / ($warningLevel - 20)) * 70;
        }
        
        return 50;
    }
    
    /**
     * Check if oxygenator should run based on interval
     */
    private function checkOxygenatorInterval($settings)
    {
        $interval = $settings['oxygenator_interval'] ?? 0;
        
        if ($interval <= 0) {
            return 0; // No interval-based activation
        }
        
        // Check last oxygenator ON time
        $lastOnLog = $this->deviceLogModel
            ->where('device_name', 'oxygenator')
            ->where('action', 'ON')
            ->orderBy('created_at', 'DESC')
            ->first();
        
        if (!$lastOnLog) {
            return 100; // Never been on, should turn on
        }
        
        $lastOnTime = strtotime($lastOnLog['created_at']);
        $currentTime = time();
        $minutesSinceLastOn = ($currentTime - $lastOnTime) / 60;
        
        // Calculate score based on interval
        if ($minutesSinceLastOn >= $interval) {
            return 100; // Time to turn on
        } elseif ($minutesSinceLastOn >= $interval * 0.8) {
            return 75; // Almost time
        } elseif ($minutesSinceLastOn >= $interval * 0.5) {
            return 50; // Halfway
        }
        
        return 25; // Recently turned on
    }
    
    /**
     * Check if pump should run based on interval
     */
    private function checkPumpInterval($settings)
    {
        $interval = $settings['pump_interval'] ?? 0;
        
        if ($interval <= 0) {
            return 0; // No interval-based activation
        }
        
        // Check last pump ON time
        $lastOnLog = $this->deviceLogModel
            ->where('device_name', 'water_pump')
            ->where('action', 'ON')
            ->orderBy('created_at', 'DESC')
            ->first();
        
        if (!$lastOnLog) {
            return 100; // Never been on, should turn on
        }
        
        $lastOnTime = strtotime($lastOnLog['created_at']);
        $currentTime = time();
        $minutesSinceLastOn = ($currentTime - $lastOnTime) / 60;
        
        // Calculate score based on interval
        if ($minutesSinceLastOn >= $interval) {
            return 100; // Time to turn on
        } elseif ($minutesSinceLastOn >= $interval * 0.8) {
            return 75; // Almost time
        } elseif ($minutesSinceLastOn >= $interval * 0.5) {
            return 50; // Halfway
        }
        
        return 25; // Recently turned on
    }
    
    /**
     * Process auto mode when receiving sensor data
     * 
     * @param array $sensorData Sensor readings
     * @param string $deviceId Device ID
     * @return array Generated commands
     */
    public function processAutoMode($sensorData, $deviceId = 'NODEMCU_AQUASENSE_001')
    {
        // First, save sensor reading
        $sensorModel = new SensorReadingModel();
        $sensorModel->insert([
            'temperature' => $sensorData['temperature'] ?? 0,
            'ph_level' => $sensorData['ph_level'] ?? 7.0,
            'turbidity' => $sensorData['turbidity'] ?? 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Check for alerts
        $alertModel = new \App\Models\AlertModel();
        $thresholds = $this->systemSettingsModel->getThresholds();
        $alertModel->createSensorAlert($sensorData, $thresholds);
        
        // Evaluate and execute auto commands
        $commands = $this->evaluateAutoCommands($sensorData, $deviceId);
        
        return $commands;
    }
}