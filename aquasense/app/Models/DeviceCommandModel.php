<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceCommandModel extends Model
{
    protected $table = 'device_commands';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'device_name',
        'command',
        'status',
        'device_id',
        'created_at',
        'executed_at'
    ];
    
    protected $useTimestamps = false;
    
    /**
     * Add a new device command
     * 
     * @param string $deviceName Device name (oxygenator/water_pump)
     * @param string $command Command (ON/OFF)
     * @param string|null $deviceId Device ID (string, not integer)
     * @return int|bool Insert ID or false on failure
     */
    // DeviceCommandModel.php - Update addCommand method
    public function addCommand($deviceName, $command, $deviceId = null)
    {
        try {
            $data = [
                'device_name' => $deviceName,
                'command' => strtoupper($command),
                'status' => 'pending',
                'device_id' => $deviceId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            log_message('debug', 'Adding command: ' . print_r($data, true));
            
            $result = $this->insert($data);
            
            if ($result) {
                $id = $this->getInsertID();
                log_message('debug', "Command added with ID: {$id}");
                return $id;
            } else {
                log_message('error', 'Failed to insert command: ' . print_r($this->errors(), true));
                return false;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in addCommand: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Get latest commands for each device
     * 
     * @param string|null $deviceId Device ID filter
     * @return array Latest commands
     */
    public function getLatestCommands($deviceId = null)
    {
        $builder = $this->select('device_name, command, status, device_id, created_at, executed_at')
                       ->whereIn('id', function($subquery) use ($deviceId) {
                           $subquery->select('MAX(id)')
                                    ->from('device_commands')
                                    ->groupBy('device_name');
                           
                           if ($deviceId) {
                               $subquery->groupStart()
                                       ->where('device_id', $deviceId)
                                       ->orWhere('device_id IS NULL')
                                       ->groupEnd();
                           }
                       });
        
        if ($deviceId) {
            $builder->groupStart()
                   ->where('device_id', $deviceId)
                   ->orWhere('device_id IS NULL')
                   ->groupEnd();
        }
        
        return $builder->orderBy('device_name', 'ASC')
                      ->findAll();
    }
    
    /**
     * Get the latest command for a specific device
     * Priority: pending commands first, then latest executed
     * 
     * @param string $deviceName Device name
     * @param string|null $deviceId Device ID
     * @return array|null Command data or null
     */
    public function getCurrentCommand($deviceName, $deviceId = null)
    {
        try {
            // First, try to get a pending command
            $builder = $this->where('device_name', $deviceName)
                          ->where('status', 'pending')
                          ->orderBy('created_at', 'DESC')
                          ->limit(1);
            
            if ($deviceId) {
                $builder->groupStart()
                       ->where('device_id', $deviceId)
                       ->orWhere('device_id IS NULL')
                       ->groupEnd();
            }
            
            $result = $builder->first();
            
            // If no pending command, get the latest command regardless of status
            if (!$result) {
                $builder = $this->where('device_name', $deviceName)
                              ->orderBy('created_at', 'DESC')
                              ->limit(1);
                
                if ($deviceId) {
                    $builder->groupStart()
                           ->where('device_id', $deviceId)
                           ->orWhere('device_id IS NULL')
                           ->groupEnd();
                }
                
                $result = $builder->first();
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getCurrentCommand: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Mark a command as executed
     * 
     * @param int $id Command ID
     * @return bool Success status
     */
    public function markExecuted($id)
    {
        return $this->update($id, [
            'status' => 'executed',
            'executed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Mark a command as failed
     * 
     * @param int $id Command ID
     * @return bool Success status
     */
    public function markFailed($id)
    {
        return $this->update($id, [
            'status' => 'failed',
            'executed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get pending commands
     * 
     * @param string|null $deviceId Device ID filter
     * @return array Pending commands
     */
    public function getPendingCommands($deviceId = null)
    {
        $builder = $this->where('status', 'pending');
        
        if ($deviceId) {
            $builder->groupStart()
                   ->where('device_id', $deviceId)
                   ->orWhere('device_id IS NULL')
                   ->groupEnd();
        }
        
        return $builder->orderBy('created_at', 'ASC')
                      ->findAll();
    }
    
    /**
     * Get commands by device name
     * 
     * @param string $deviceName Device name
     * @param int $limit Number of records
     * @return array Device commands
     */
    public function getCommandsByDevice($deviceName, $limit = 10)
    {
        return $this->where('device_name', $deviceName)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Clean up old executed commands
     * 
     * @param int $daysOld Delete commands older than X days
     * @return bool Success status
     */
    public function cleanupOldCommands($daysOld = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-$daysOld days"));
        
        return $this->where('status', 'executed')
                   ->where('created_at <', $cutoffDate)
                   ->delete();
    }
    
    /**
     * Get command statistics
     * 
     * @param string|null $deviceId Device ID filter
     * @param string $period Time period (today, week, month)
     * @return array Statistics
     */
    public function getCommandStats($deviceId = null, $period = 'today')
    {
        $periods = [
            'today' => 'DATE(created_at) = CURDATE()',
            'week' => 'created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)',
            'month' => 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)'
        ];
        
        $where = $periods[$period] ?? $periods['today'];
        
        $builder = $this->select("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'executed' THEN 1 ELSE 0 END) as executed,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                COUNT(DISTINCT device_name) as unique_devices
            ")
            ->where($where);
        
        if ($deviceId) {
            $builder->groupStart()
                   ->where('device_id', $deviceId)
                   ->orWhere('device_id IS NULL')
                   ->groupEnd();
        }
        
        return $builder->first() ?? [
            'total' => 0,
            'pending' => 0,
            'executed' => 0,
            'failed' => 0,
            'unique_devices' => 0
        ];
    }
    
    /**
     * Check if duplicate command exists
     * Prevents adding the same command multiple times in short period
     * 
     * @param string $deviceName Device name
     * @param string $command Command
     * @param string|null $deviceId Device ID
     * @param int $minutes Time window in minutes
     * @return bool True if duplicate exists
     */
    public function isDuplicateCommand($deviceName, $command, $deviceId = null, $minutes = 5)
    {
        $cutoffTime = date('Y-m-d H:i:s', strtotime("-$minutes minutes"));
        
        $builder = $this->where('device_name', $deviceName)
                       ->where('command', strtoupper($command))
                       ->where('status', 'pending')
                       ->where('created_at >=', $cutoffTime);
        
        if ($deviceId) {
            $builder->groupStart()
                   ->where('device_id', $deviceId)
                   ->orWhere('device_id IS NULL')
                   ->groupEnd();
        }
        
        return $builder->countAllResults() > 0;
    }
}