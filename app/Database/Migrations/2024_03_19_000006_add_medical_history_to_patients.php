<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMedicalHistoryToPatients extends Migration
{
    public function up()
    {
        $this->forge->addColumn('patients', [
            'medical_history' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'address'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('patients', 'medical_history');
    }
} 