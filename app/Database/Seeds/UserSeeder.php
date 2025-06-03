<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'email' => 'admin@clinic.com',
                'password' => password_hash('Admin@123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => '1234567890',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'doctor',
                'email' => 'doctor@clinic.com',
                'password' => password_hash('Doctor@123', PASSWORD_DEFAULT),
                'role' => 'doctor',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '2345678901',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'receptionist',
                'email' => 'receptionist@clinic.com',
                'password' => password_hash('Reception@123', PASSWORD_DEFAULT),
                'role' => 'receptionist',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone' => '3456789012',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('users')->insertBatch($data);

        // Add doctor details for the doctor user
        $doctorUserId = $this->db->table('users')
            ->where('username', 'doctor')
            ->get()
            ->getRow()
            ->id;

        $doctorData = [
            'user_id' => $doctorUserId,
            'specialty' => 'General Medicine',
            'qualification' => 'MBBS, MD',
            'license_number' => 'DOC123456',
            'consultation_fee' => 100.00,
            'available_days' => 'Monday,Tuesday,Wednesday,Thursday,Friday',
            'available_time_start' => '09:00:00',
            'available_time_end' => '17:00:00',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('doctors')->insert($doctorData);
    }
} 