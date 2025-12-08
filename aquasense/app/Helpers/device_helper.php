<?php

if (!function_exists('store_device_command')) {
    function store_device_command($device, $action)
    {
        $session = session();
        
        // Get existing commands or initialize
        $commands = $session->get('device_commands');
        if (!$commands) {
            $commands = [
                'oxygenator' => 0,
                'water_pump' => 0,
                'mode' => 'auto'
            ];
        }
        
        // Log for debugging
        log_message('debug', "Helper: Storing command {$device} => {$action}");
        
        if ($device === 'oxygenator' || $device === 'water_pump') {
            $commands[$device] = ($action === 'on') ? 1 : 0;
        } elseif ($device === 'mode') {
            $commands['mode'] = $action;
        }
        
        $session->set('device_commands', $commands);
        log_message('debug', 'Helper: Commands in session: ' . print_r($commands, true));
        
        return true;
    }
}