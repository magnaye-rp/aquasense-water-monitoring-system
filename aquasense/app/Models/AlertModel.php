<?php

namespace App\Models;

use CodeIgniter\Model;

class AlertModel extends Model
{
    protected $table            = 'alerts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'type',
        'message',
        'level',      // Added 'level' field
        'created_at',
        'is_read'     // Added 'is_read' field
    ];

    protected $useTimestamps = false;

    /**
     * Create a new alert
     * 
     * @param string $type Alert type
     * @param string $message Alert message
     * @param string $level Alert level (info, warning, danger)
     * @return int|bool Insert ID or false on failure
     */
    public function createAlert($type, $message, $level = 'info')
    {
        return $this->insert([
            'type' => $type,
            'message' => $message,
            'level' => $level,
            'created_at' => date('Y-m-d H:i:s'),
            'is_read' => 0
        ]);
    }

    /**
     * Create sensor alert based on readings
     * 
     * @param array $reading Sensor reading data
     * @param array $thresholds Threshold values
     * @return bool Whether an alert was created
     */
    public function createSensorAlert($reading, $thresholds)
    {
        $alerts = [];
        $level = 'info';

        // Check temperature
        if ($reading['temperature'] < $thresholds['temp_min']) {
            $alerts[] = "Low temperature: {$reading['temperature']}°C";
            $level = 'warning';
        } elseif ($reading['temperature'] > $thresholds['temp_max']) {
            $alerts[] = "High temperature: {$reading['temperature']}°C";
            $level = 'warning';
        }

        // Check pH
        if ($reading['ph_level'] < $thresholds['ph_min']) {
            $alerts[] = "Low pH level: {$reading['ph_level']}";
            $level = 'danger';
        } elseif ($reading['ph_level'] > $thresholds['ph_max']) {
            $alerts[] = "High pH level: {$reading['ph_level']}";
            $level = 'danger';
        }

        // Check turbidity
        if ($reading['turbidity'] > $thresholds['turbidity_max']) {
            $alerts[] = "High turbidity: {$reading['turbidity']} NTU";
            $level = 'warning';
        }

        if (!empty($alerts)) {
            $message = implode('. ', $alerts);
            $this->createAlert('sensor', $message, $level);
            return true;
        }

        return false;
    }

    /**
     * Get unread alerts count
     * 
     * @return int Number of unread alerts
     */
    public function getUnreadCount()
    {
        return $this->where('is_read', 0)->countAllResults();
    }

    /**
     * Mark alerts as read
     * 
     * @param array|int $ids Alert ID(s)
     * @return bool
     */
    public function markAsRead($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        
        return $this->whereIn('id', $ids)
                    ->set(['is_read' => 1])
                    ->update();
    }

    /**
     * Mark all alerts as read
     * 
     * @return bool
     */
    public function markAllAsRead()
    {
        return $this->where('is_read', 0)
                    ->set(['is_read' => 1])
                    ->update();
    }

    /**
     * Get alerts by level
     * 
     * @param string $level Alert level
     * @param int $limit Number of alerts
     * @return array
     */
    public function getByLevel($level, $limit = 20)
    {
        return $this->where('level', $level)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get recent alerts with pagination
     * 
     * @param int $perPage Items per page
     * @param int $page Current page
     * @return array Alerts with pagination info
     */
    public function getPaginatedAlerts($perPage = 20, $page = 1)
    {
        $total = $this->countAll();
        $offset = ($page - 1) * $perPage;
        
        $alerts = $this->orderBy('created_at', 'DESC')
                       ->limit($perPage, $offset)
                       ->findAll();
        
        return [
            'alerts' => $alerts,
            'pager' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ];
    }
}