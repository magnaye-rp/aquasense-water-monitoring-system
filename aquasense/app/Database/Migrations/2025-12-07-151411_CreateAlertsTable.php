<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlertsTable extends Migration
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
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'level' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'is_read' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
        ]);

        // Use IF NOT EXISTS so rerunning migrations doesn't fail if the table is already present
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('alerts', true);
    }

    public function down()
    {
        $this->forge->dropTable('alerts');
    }
}
