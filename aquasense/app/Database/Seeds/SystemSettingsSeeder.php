<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'water_type'            => 'generic',
            'oxygenator_auto'       => 0,
            'pump_auto'             => 0,
            'oxygenator_interval'   => 60,
            'pump_interval'         => 60,
            'created_at'            => date('Y-m-d H:i:s'),
        ];

        $this->db->table('system_settings')->insert($data);
    }
}
