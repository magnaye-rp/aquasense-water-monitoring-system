<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemSettingsTable extends Migration
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
            'water_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'oxygenator_auto' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'pump_auto' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'oxygenator_interval' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'pump_interval' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ph_good_min' => [
                'type' => 'FLOAT',
                'default' => 6.5,
            ],
            'ph_good_max' => [
                'type' => 'FLOAT',
                'default' => 8.5,
            ],
            'turbidity_limit' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 100,
            ],
            'temperature_range' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
        ]);

        // Use IF NOT EXISTS so rerunning migrations is safe
        $this->forge->addKey('id', true);
        $this->forge->createTable('system_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('system_settings');
    }
}
