<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceLogModel extends Model
{
    protected $table            = 'device_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'device_name',   // oxygenator / water_pump
        'action',        // ON / OFF
        'triggered_by',  // manual / auto
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Log device action
     * 
     * @param string $deviceName Device name (oxygenator/water_pump)
     * @param string $action Action (ON/OFF)
     * @param string $triggeredBy Who triggered (manual/auto)
     * @return int|bool Insert ID or false on failure
     */
    public function logDeviceAction($deviceName, $action, $triggeredBy = 'auto')
    {
        // First, check if we need to create the table
        if (!$this->db->tableExists($this->table)) {
            // You might want to create the table programmatically or through migration
            return false;
        }

        return $this->insert([
            'device_name' => $deviceName,
            'action' => strtoupper($action),
            'triggered_by' => $triggeredBy,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get device status history
     * 
     * @param string $deviceName Device name
     * @param int $hours History for last X hours
     * @return array Device logs
     */
    public function getDeviceHistory($deviceName, $hours = 24)
    {
        return $this->where('device_name', $deviceName)
                    ->where("created_at >= DATE_SUB(NOW(), INTERVAL $hours HOUR)")
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get latest device state
     * 
     * @param string $deviceName Device name
     * @return array|null Latest log entry or null
     */
    public function getCurrentState($deviceName)
    {
        return $this->where('device_name', $deviceName)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    /**
     * Check if device should be activated based on sensor data
     * 
     * @param string $deviceName Device name
     * @param array $sensorData Current sensor readings
     * @param array $settings System settings
     * @return bool Whether device should be ON
     */
    public function shouldActivateDevice($deviceName, $sensorData, $settings)
    {
        if ($deviceName === 'oxygenator') {
            // Oxygenator logic (activate when oxygen levels are low or temperature is high)
            if ($sensorData['temperature'] > ($settings['temp_max'] ?? 28)) {
                return true;
            }
        } elseif ($deviceName === 'water_pump') {
            // Water pump logic (activate based on turbidity or regular intervals)
            if ($sensorData['turbidity'] > ($settings['turbidity_limit'] ?? 50)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get device usage statistics
     * 
     * @param string $deviceName Device name
     * @param string $period Period (today, week, month)
     * @return array Usage statistics
     */
    public function getUsageStatistics($deviceName, $period = 'today')
    {
        $periods = [
            'today' => 'DATE(created_at) = CURDATE()',
            'week' => 'created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)',
            'month' => 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)'
        ];

        $condition = $periods[$period] ?? $periods['today'];

        $query = $this->db->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as total_actions,
                SUM(CASE WHEN action = 'ON' THEN 1 ELSE 0 END) as on_count,
                SUM(CASE WHEN action = 'OFF' THEN 1 ELSE 0 END) as off_count,
                SUM(CASE WHEN triggered_by = 'auto' THEN 1 ELSE 0 END) as auto_count,
                SUM(CASE WHEN triggered_by = 'manual' THEN 1 ELSE 0 END) as manual_count
            FROM device_logs
            WHERE device_name = ? AND $condition
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ", [$deviceName]);

        return $query->getResultArray();
    }

    /**
     * Get device uptime percentage
     * 
     * @param string $deviceName Device name
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return float Uptime percentage
     */
    public function getUptimePercentage($deviceName, $startDate, $endDate)
    {
        $logs = $this->where('device_name', $deviceName)
                    ->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();

        if (empty($logs)) {
            return 0;
        }

        $totalTime = strtotime($endDate) - strtotime($startDate);
        $onTime = 0;
        $lastOnTime = null;

        foreach ($logs as $log) {
            if ($log['action'] === 'ON' && $lastOnTime === null) {
                $lastOnTime = strtotime($log['created_at']);
            } elseif ($log['action'] === 'OFF' && $lastOnTime !== null) {
                $onTime += (strtotime($log['created_at']) - $lastOnTime);
                $lastOnTime = null;
            }
        }

        // If device was ON at the end of period
        if ($lastOnTime !== null) {
            $onTime += (strtotime($endDate) - $lastOnTime);
        }

        return ($totalTime > 0) ? ($onTime / $totalTime) * 100 : 0;
    }
}