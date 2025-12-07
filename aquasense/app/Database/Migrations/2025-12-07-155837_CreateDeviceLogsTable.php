<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeviceLogsTable extends Migration
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
                'comment' => 'oxygenator / water_pump',
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'comment' => 'ON / OFF',
            ],
            'triggered_by' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'comment' => 'manual / auto',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('device_name');
        $this->forge->addKey('created_at');
        $this->forge->createTable('device_logs', true);

        // Insert initial data for testing (optional)
        $data = [
            [
                'device_name' => 'oxygenator',
                'action' => 'ON',
                'triggered_by' => 'auto',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
            [
                'device_name' => 'water_pump',
                'action' => 'OFF',
                'triggered_by' => 'manual',
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
            ],
        ];

        $this->db->table('device_logs')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('device_logs', true);
    }
}