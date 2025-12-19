<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingsModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'water_type',
        'oxygenator_auto',
        'pump_auto',
        'oxygenator_interval',
        'pump_interval',
        'created_at',
        'updated_at',
        'ph_good_min',
        'ph_good_max',
        'turbidity_limit',
        'temperature_range',
        'email_alerts',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get current system settings
     * 
     * @return array|null Settings array or null if not found
     */
    public function getCurrentSettings()
    {
        return $this->first();
    }

    /**
     * Update system settings
     * 
     * @param array $data Settings data to update
     * @return bool
     */
    public function updateSettings($data)
    {
        $current = $this->first();
        
        if ($current) {
            return $this->update($current['id'], $data);
        }
        
        return $this->insert($data) !== false;
    }

    /**
     * Get temperature range as array
     * 
     * @return array Array with min and max temperature
     */
    public function getTemperatureRange()
    {
        $settings = $this->first();
        
        if ($settings && !empty($settings['temperature_range'])) {
            $range = explode('-', $settings['temperature_range']);
            if (count($range) === 2) {
                return [
                    'min' => floatval(trim($range[0])),
                    'max' => floatval(trim($range[1]))
                ];
            }
        }
        
        // Default temperature range
        return ['min' => 20, 'max' => 30];
    }

    /**
     * Set temperature range from array
     * 
     * @param array $range Array with 'min' and 'max' keys
     * @return bool
     */
    public function setTemperatureRange($range)
    {
        if (isset($range['min']) && isset($range['max'])) {
            $temperatureRange = $range['min'] . '-' . $range['max'];
            return $this->updateSettings(['temperature_range' => $temperatureRange]);
        }
        
        return false;
    }

    /**
     * Get all thresholds for water quality checks
     * 
     * @return array Complete thresholds array
     */
    public function getThresholds()
    {
        $settings = $this->first();
        
        if (!$settings) {
            return $this->getDefaultThresholds();
        }

        $tempRange = $this->getTemperatureRange();
        
        return [
            'temp_min' => $tempRange['min'],
            'temp_max' => $tempRange['max'],
            'ph_min' => $settings['ph_good_min'],
            'ph_max' => $settings['ph_good_max'],
            'turbidity_max' => $settings['turbidity_limit']
        ];
    }

    /**
     * Get default thresholds
     * 
     * @return array Default threshold values
     */
    private function getDefaultThresholds()
    {
        return [
            'temp_min' => 20,
            'temp_max' => 30,
            'ph_min' => 6.5,
            'ph_max' => 8.5,
            'turbidity_max' => 100
        ];
    }

    /**
     * Check if automatic mode is enabled for devices
     * 
     * @return array Status for each device
     */
    public function getAutoModeStatus()
    {
        $settings = $this->first();
        
        if (!$settings) {
            return [
                'oxygenator' => false,
                'pump' => false
            ];
        }

        return [
            'oxygenator' => (bool)$settings['oxygenator_auto'],
            'pump' => (bool)$settings['pump_auto']
        ];
    }

    /**
     * Get water type information
     * 
     * @return array Water type data
     */
    public function getWaterTypeInfo()
    {
        $settings = $this->first();
        $waterType = $settings['water_type'] ?? 'generic';

        $types = [
            'freshwater' => [
                'name' => 'Freshwater',
                'temp_min' => 20,
                'temp_max' => 28,
                'ph_min' => 6.5,
                'ph_max' => 7.5,
                'description' => 'Ideal for most freshwater fish'
            ],
            'saltwater' => [
                'name' => 'Saltwater',
                'temp_min' => 24,
                'temp_max' => 28,
                'ph_min' => 7.8,
                'ph_max' => 8.4,
                'description' => 'Marine aquarium settings'
            ],
            'generic' => [
                'name' => 'Generic',
                'temp_min' => 20,
                'temp_max' => 30,
                'ph_min' => 6.5,
                'ph_max' => 8.5,
                'description' => 'General water monitoring'
            ]
        ];

        return $types[$waterType] ?? $types['generic'];
    }
}