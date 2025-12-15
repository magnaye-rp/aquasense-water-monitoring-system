<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'class',
        'key',
        'value',
        'type',
        'context'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get setting by class and key
     * 
     * @param string $class Setting class
     * @param string $key Setting key
     * @param string|null $context Context filter
     * @return array|null Setting data
     */
    public function getSetting($class, $key, $context = null)
    {
        $builder = $this->where('class', $class)
                       ->where('key', $key);
        
        if ($context !== null) {
            $builder->where('context', $context);
        }
        
        return $builder->first();
    }
    
    /**
     * Get setting value (with type casting)
     * 
     * @param string $class Setting class
     * @param string $key Setting key
     * @param mixed $default Default value if not found
     * @param string|null $context Context filter
     * @return mixed Setting value
     */
    public function getValue($class, $key, $default = null, $context = null)
    {
        $setting = $this->getSetting($class, $key, $context);
        
        if (!$setting) {
            return $default;
        }
        
        return $this->castValue($setting['value'], $setting['type']);
    }
    
    /**
     * Set setting value
     * 
     * @param string $class Setting class
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @param string|null $context Context
     * @return bool Success status
     */
    public function setValue($class, $key, $value, $context = null)
    {
        $type = $this->determineType($value);
        
        $data = [
            'class' => $class,
            'key' => $key,
            'value' => is_string($value) ? $value : json_encode($value),
            'type' => $type,
            'context' => $context
        ];
        
        $existing = $this->getSetting($class, $key, $context);
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        }
        
        return $this->insert($data);
    }
    
    /**
     * Get all settings for a class
     * 
     * @param string $class Setting class
     * @param string|null $context Context filter
     * @return array Settings
     */
    public function getClassSettings($class, $context = null)
    {
        $builder = $this->where('class', $class);
        
        if ($context !== null) {
            $builder->where('context', $context);
        }
        
        $settings = $builder->findAll();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }
        
        return $result;
    }
    
    /**
     * Delete setting
     * 
     * @param string $class Setting class
     * @param string $key Setting key
     * @param string|null $context Context filter
     * @return bool Success status
     */
    public function deleteSetting($class, $key, $context = null)
    {
        $builder = $this->where('class', $class)
                       ->where('key', $key);
        
        if ($context !== null) {
            $builder->where('context', $context);
        }
        
        return $builder->delete();
    }
    
    /**
     * Cast value based on type
     * 
     * @param string $value String value
     * @param string $type Value type
     * @return mixed Casted value
     */
    private function castValue($value, $type)
    {
        switch ($type) {
            case 'integer':
            case 'int':
                return (int)$value;
                
            case 'float':
            case 'double':
                return (float)$value;
                
            case 'boolean':
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                
            case 'array':
            case 'json':
                return json_decode($value, true) ?? [];
                
            case 'object':
                return json_decode($value) ?? new \stdClass();
                
            case 'null':
                return null;
                
            default:
                return $value; // string
        }
    }
    
    /**
     * Determine type from value
     * 
     * @param mixed $value Value
     * @return string Type
     */
    private function determineType($value)
    {
        if (is_int($value)) {
            return 'integer';
        }
        
        if (is_float($value)) {
            return 'float';
        }
        
        if (is_bool($value)) {
            return 'boolean';
        }
        
        if (is_array($value) || is_object($value)) {
            return 'json';
        }
        
        if (is_null($value)) {
            return 'null';
        }
        
        return 'string';
    }
    
    /**
     * Get settings grouped by class
     * 
     * @param string|null $context Context filter
     * @return array Grouped settings
     */
    public function getGroupedSettings($context = null)
    {
        $builder = $this->orderBy('class', 'ASC')
                       ->orderBy('key', 'ASC');
        
        if ($context !== null) {
            $builder->where('context', $context);
        }
        
        $settings = $builder->findAll();
        
        $grouped = [];
        foreach ($settings as $setting) {
            $class = $setting['class'];
            $key = $setting['key'];
            
            if (!isset($grouped[$class])) {
                $grouped[$class] = [];
            }
            
            $grouped[$class][$key] = [
                'value' => $this->castValue($setting['value'], $setting['type']),
                'type' => $setting['type'],
                'context' => $setting['context'],
                'created_at' => $setting['created_at'],
                'updated_at' => $setting['updated_at']
            ];
        }
        
        return $grouped;
    }
    
    /**
     * Import multiple settings at once
     * 
     * @param array $settings Array of settings [class => [key => value]]
     * @param string|null $context Context
     * @return bool Success status
     */
    public function importSettings($settings, $context = null)
    {
        $success = true;
        
        foreach ($settings as $class => $classSettings) {
            foreach ($classSettings as $key => $value) {
                if (!$this->setValue($class, $key, $value, $context)) {
                    $success = false;
                }
            }
        }
        
        return $success;
    }
}