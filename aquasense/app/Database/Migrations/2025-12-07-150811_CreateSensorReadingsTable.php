<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSensorReadingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'temperature' => [
                'type' => 'FLOAT',
            ],
            'ph_level' => [
                'type' => 'FLOAT',
            ],
            'turbidity' => [
                'type' => 'FLOAT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        // Use IF NOT EXISTS so rerunning migrations is safe
        $this->forge->addKey('id', true);
        $this->forge->createTable('sensor_readings', true);
    }

    public function down()
    {
        $this->forge->dropTable('sensor_readings');
    }
}
