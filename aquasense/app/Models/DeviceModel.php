<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceModel extends Model
{
    protected $table = 'devices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'name',
        'type',
        'location',
        'status',
        'last_seen'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'type' => 'required|max_length[50]',
        'status' => 'permit_empty|in_list[online,offline,maintenance]'
    ];
}