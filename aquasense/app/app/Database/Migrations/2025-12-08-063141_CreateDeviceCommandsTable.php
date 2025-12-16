<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeviceCommandsTable extends Migration
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
            'device_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'oxygenator/water_pump',
            ],
            'command' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'comment' => 'ON/OFF',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'executed', 'failed'],
                'default' => 'pending',
            ],
            'device_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Which device should execute this',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'executed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['device_id', 'status']);
        $this->forge->addKey('created_at');
        $this->forge->createTable('device_commands', true);

        // Insert test data
        $this->db->table('device_commands')->insertBatch([
            [
                'device_name' => 'oxygenator',
                'command' => 'ON',
                'status' => 'pending',
                'device_id' => 'NODEMCU_AQUASENSE_001',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'device_name' => 'water_pump',
                'command' => 'OFF',
                'status' => 'pending',
                'device_id' => 'NODEMCU_AQUASENSE_001',
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('device_commands', true);
    }
}