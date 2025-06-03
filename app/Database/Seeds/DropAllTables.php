<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DropAllTables extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Get all tables
        $tables = $this->db->query("SHOW TABLES")->getResultArray();

        // Drop each table
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            $this->db->query("DROP TABLE IF EXISTS `{$tableName}`");
        }

        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
} 