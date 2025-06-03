<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDoctorsTable extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'specialty' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'qualification' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'license_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'consultation_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'available_days' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Comma-separated days of the week',
            ],
            'available_time_start' => [
                'type' => 'TIME',
            ],
            'available_time_end' => [
                'type' => 'TIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctors');
    }

    public function down()
    {
        $this->forge->dropTable('doctors');
    }
} 