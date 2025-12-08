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
    
    public function addCommand($deviceName, $command, $deviceId = null)
    {
        $data = [
            'device_name' => $deviceName,
            'command' => strtoupper($command),
            'status' => 'pending',
            'device_id' => $deviceId,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Insert and return the insert ID
        if ($this->insert($data)) {
            return $this->getInsertID();
        }
        
        return false;
    }
    
    public function getLatestCommands($deviceId = null)
    {
        // Use subquery to get the latest command for each device
        $subquery = $this->select('device_name, MAX(created_at) as latest_time')
                         ->groupBy('device_name');
        
        if ($deviceId) {
            $subquery->groupStart()
                    ->where('device_id', $deviceId)
                    ->orWhere('device_id IS NULL')
                    ->groupEnd();
        }
        
        $latestCommands = $subquery->findAll();
        
        // Now get the full records for these latest commands
        $commands = [];
        foreach ($latestCommands as $latest) {
            $builder = $this->where('device_name', $latest['device_name'])
                           ->where('created_at', $latest['latest_time']);
            
            if ($deviceId) {
                $builder->groupStart()
                       ->where('device_id', $deviceId)
                       ->orWhere('device_id IS NULL')
                       ->groupEnd();
            }
            
            $command = $builder->first();
            if ($command) {
                $commands[] = $command;
            }
        }
        
        return $commands;
    }
    
    public function getLatestDeviceCommands($deviceId = null)
    {
        // Simplified version: Get latest command for each device
        $sql = "SELECT dc1.* 
                FROM device_commands dc1
                INNER JOIN (
                    SELECT device_name, MAX(created_at) as max_date
                    FROM device_commands
                    WHERE (? IS NULL OR device_id = ? OR device_id IS NULL)
                    GROUP BY device_name
                ) dc2 ON dc1.device_name = dc2.device_name AND dc1.created_at = dc2.max_date
                WHERE ? IS NULL OR dc1.device_id = ? OR dc1.device_id IS NULL
                ORDER BY dc1.device_name";
        
        return $this->db->query($sql, [$deviceId, $deviceId, $deviceId, $deviceId])->getResultArray();
    }
    
    public function markExecuted($id)
    {
        return $this->update($id, [
            'status' => 'executed',
            'executed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get the current/latest command for a specific device
     * First checks for pending commands, then falls back to the latest executed command
     */
    public function getCurrentCommand($deviceName, $deviceId = null)
    {
        try {
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
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in getCurrentCommand: ' . $e->getMessage());
            return null;
        }
    }

    public function getLatestCommandsForDevice($deviceId = null)
    {
        $query = $this->select('device_name, command')
                    ->where('device_id', $deviceId)
                    ->orWhere('device_id IS NULL')
                    ->groupBy('device_name')
                    ->orderBy('created_at', 'DESC');
        
        return $query->findAll();
    }

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

}