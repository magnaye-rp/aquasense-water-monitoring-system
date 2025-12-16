<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SampleSensorDataSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 20; $i++) {
            $this->db->table('sensor_readings')->insert([
                'temperature' => rand(250, 300) / 10,  // 25.0 to 30.0
                'ph_level'    => rand(65, 85) / 10,   // 6.5 to 8.5
                'turbidity'   => rand(5, 50) / 1,     // 5 to 50 NTU
                'created_at'  => date('Y-m-d H:i:s', strtotime("-{$i} minutes"))
            ]);
        }
    }
}
