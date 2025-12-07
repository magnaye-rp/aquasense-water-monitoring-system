<?php

namespace App\Models;

use CodeIgniter\Model;

class SensorReadingModel extends Model
{
    protected $table            = 'sensor_readings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'temperature',
        'ph_level',  // Changed from 'ph_value' to 'ph_level'
        'turbidity',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get latest sensor readings
     * 
     * @param int $limit Number of records to retrieve
     * @return array
     */
    public function getLatestReadings($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get current water quality status based on thresholds
     * 
     * @param array $thresholds Array with threshold values
     * @return array Status information
     */
    public function getCurrentStatus($thresholds = null)
    {
        $latest = $this->orderBy('created_at', 'DESC')->first();
        
        if (!$latest) {
            return [
                'status' => 'no_data',
                'message' => 'No sensor data available',
                'latest' => null
            ];
        }

        $status = 'good';
        $issues = [];

        // Check temperature
        if (isset($thresholds['temp_min']) && $latest['temperature'] < $thresholds['temp_min']) {
            $status = 'warning';
            $issues[] = "Temperature is low ({$latest['temperature']}°C)";
        }
        if (isset($thresholds['temp_max']) && $latest['temperature'] > $thresholds['temp_max']) {
            $status = 'warning';
            $issues[] = "Temperature is high ({$latest['temperature']}°C)";
        }

        // Check pH level
        if (isset($thresholds['ph_min']) && $latest['ph_level'] < $thresholds['ph_min']) {
            $status = 'danger';
            $issues[] = "pH level is too low ({$latest['ph_level']})";
        }
        if (isset($thresholds['ph_max']) && $latest['ph_level'] > $thresholds['ph_max']) {
            $status = 'danger';
            $issues[] = "pH level is too high ({$latest['ph_level']})";
        }

        // Check turbidity
        if (isset($thresholds['turbidity_max']) && $latest['turbidity'] > $thresholds['turbidity_max']) {
            $status = 'warning';
            $issues[] = "Water is turbid ({$latest['turbidity']} NTU)";
        }

        return [
            'status' => $status,
            'latest' => $latest,
            'issues' => $issues,
            'message' => empty($issues) ? 'All parameters within normal range' : implode(', ', $issues)
        ];
    }

    /**
     * Get sensor statistics for a specific period
     * 
     * @param string $period Period in SQL format (e.g., '1 HOUR', '1 DAY', '7 DAY')
     * @return array Statistics
     */
    public function getStatistics($period = '1 DAY')
    {
        $query = $this->db->query("
            SELECT 
                AVG(temperature) as avg_temperature,
                MIN(temperature) as min_temperature,
                MAX(temperature) as max_temperature,
                AVG(ph_level) as avg_ph,
                MIN(ph_level) as min_ph,
                MAX(ph_level) as max_ph,
                AVG(turbidity) as avg_turbidity,
                MIN(turbidity) as min_turbidity,
                MAX(turbidity) as max_turbidity,
                COUNT(*) as total_readings
            FROM sensor_readings
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period)
        ");
        
        return $query->getRowArray();
    }

    /**
     * Get chart data for visualization
     * 
     * @param string $period Period in SQL format
     * @param int $limit Number of data points
     * @return array Formatted chart data
     */
    public function getChartData($period = '1 DAY', $limit = 24)
    {
        $query = $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%H:%i') as time,
                temperature,
                ph_level,
                turbidity
            FROM sensor_readings
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period)
            ORDER BY created_at DESC
            LIMIT ?
        ", [$limit]);
        
        $data = $query->getResultArray();
        return array_reverse($data); // Reverse to show chronological order
    }
}