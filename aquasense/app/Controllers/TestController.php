<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function index()
    {
        return view('test/db_test');
    }
    
    public function dbTest()
    {
        // Enable error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Get database connection
        $db = \Config\Database::connect();
        
        $output = "<h1>Database Connection Test</h1>";
        
        // Test 1: Check connection
        $output .= "<h2>1. Database Connection</h2>";
        $output .= "Connected: " . ($db->connID ? "<span style='color:green'>YES ✓</span>" : "<span style='color:red'>NO ✗</span>") . "<br>";
        $output .= "Database: " . $db->database . "<br>";
        
        // Test 2: List all tables
        $output .= "<h2>2. Tables in Database</h2>";
        $tables = $db->listTables();
        $output .= "Total tables: " . count($tables) . "<br>";
        $output .= "<ul>";
        foreach ($tables as $table) {
            $output .= "<li>$table</li>";
        }
        $output .= "</ul>";
        
        // Test 3: Check device_commands table
        $output .= "<h2>3. Device Commands Table</h2>";
        if (in_array('device_commands', $tables)) {
            $output .= "<span style='color:green'>Table exists ✓</span><br>";
            
            // Show table structure
            $fields = $db->getFieldData('device_commands');
            $output .= "<h3>Table Structure:</h3>";
            $output .= "<table border='1' cellpadding='5'>";
            $output .= "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            foreach ($fields as $field) {
                $output .= "<tr>";
                $output .= "<td>{$field->name}</td>";
                $output .= "<td>{$field->type}</td>";
                $output .= "<td>{$field->null}</td>";
                $output .= "<td>{$field->primary_key}</td>";
                $output .= "<td>{$field->default}</td>";
                $output .= "<td>{$field->extra}</td>";
                $output .= "</tr>";
            }
            $output .= "</table>";
            
            // Test insert
            $output .= "<h3>4. Test Insert Command</h3>";
            try {
                $result = $db->table('device_commands')->insert([
                    'device_name' => 'oxygenator',
                    'command' => 'ON',
                    'status' => 'pending',
                    'device_id' => 'TEST_001',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                $output .= "Insert success: " . ($result ? "<span style='color:green'>YES ✓</span>" : "<span style='color:red'>NO ✗</span>") . "<br>";
                $output .= "Insert ID: " . $db->insertID() . "<br>";
                
                // Show all pending commands
                $output .= "<h3>5. All Pending Commands</h3>";
                $pending = $db->table('device_commands')
                             ->where('status', 'pending')
                             ->orderBy('created_at', 'DESC')
                             ->get()
                             ->getResultArray();
                
                $output .= "Count: " . count($pending) . "<br>";
                if ($pending) {
                    $output .= "<table border='1' cellpadding='5'>";
                    $output .= "<tr><th>ID</th><th>Device</th><th>Command</th><th>Status</th><th>Device ID</th><th>Created At</th></tr>";
                    foreach ($pending as $cmd) {
                        $output .= "<tr>";
                        $output .= "<td>{$cmd['id']}</td>";
                        $output .= "<td>{$cmd['device_name']}</td>";
                        $output .= "<td>{$cmd['command']}</td>";
                        $output .= "<td>{$cmd['status']}</td>";
                        $output .= "<td>{$cmd['device_id']}</td>";
                        $output .= "<td>{$cmd['created_at']}</td>";
                        $output .= "</tr>";
                    }
                    $output .= "</table>";
                }
                
            } catch (\Exception $e) {
                $output .= "<span style='color:red'>Insert failed: " . $e->getMessage() . "</span><br>";
            }
            
        } else {
            $output .= "<span style='color:red'>Table doesn't exist ✗</span><br>";
            $output .= "<h3>Create table with this SQL:</h3>";
            $output .= "<pre>";
            $output .= "CREATE TABLE `device_commands` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_name` varchar(50) NOT NULL COMMENT 'oxygenator/water_pump',
  `command` varchar(10) NOT NULL COMMENT 'ON/OFF',
  `status` enum('pending','executed','failed') NOT NULL DEFAULT 'pending',
  `device_id` varchar(100) DEFAULT NULL COMMENT 'Which device should execute this',
  `created_at` datetime NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_id_status` (`device_id`,`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $output .= "</pre>";
        }
        
        // Test 6: Check sensor_readings table
        $output .= "<h2>6. Sensor Readings (Last 5)</h2>";
        if (in_array('sensor_readings', $tables)) {
            $readings = $db->table('sensor_readings')
                          ->orderBy('id', 'DESC')
                          ->limit(5)
                          ->get()
                          ->getResultArray();
            
            $output .= "Count: " . count($readings) . "<br>";
            if ($readings) {
                $output .= "<table border='1' cellpadding='5'>";
                $output .= "<tr><th>ID</th><th>Temp</th><th>pH</th><th>Turbidity</th><th>Created At</th></tr>";
                foreach ($readings as $reading) {
                    $output .= "<tr>";
                    $output .= "<td>{$reading['id']}</td>";
                    $output .= "<td>{$reading['temperature']}°C</td>";
                    $output .= "<td>{$reading['ph_level']}</td>";
                    $output .= "<td>{$reading['turbidity']} NTU</td>";
                    $output .= "<td>{$reading['created_at']}</td>";
                    $output .= "</tr>";
                }
                $output .= "</table>";
            }
        }
        
        echo $output;
    }
}