<?php

namespace App\Models;

class PatientModel extends BaseModel
{
    protected $table = 'patients';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'blood_type',
        'allergies',
        'medical_conditions',
        'medical_history'
    ];

    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[50]',
        'last_name' => 'required|min_length[2]|max_length[50]',
        'email' => 'required|valid_email|is_unique[patients.email,id,{id}]',
        'phone' => 'required|min_length[10]|max_length[15]',
        'date_of_birth' => 'required|valid_date',
        'gender' => 'required|in_list[male,female,other]',
        'address' => 'required|min_length[10]|max_length[255]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered to another patient'
        ]
    ];

    protected $beforeInsert = ['setDefaults'];
    protected $beforeUpdate = ['setDefaults'];

    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['medical_history'])) {
            $data['data']['medical_history'] = '';
        }
        return $data;
    }

    /**
     * Get patient's full name
     */
    public function getFullName(array $patient): string
    {
        return $patient['first_name'] . ' ' . $patient['last_name'];
    }

    /**
     * Calculate patient's age
     */
    public function getAge(array $patient): int
    {
        return date_diff(date_create($patient['date_of_birth']), date_create('now'))->y;
    }

    /**
     * Search patients by name or phone
     */
    public function search(string $term)
    {
        return $this->like('first_name', $term)
                    ->orLike('last_name', $term)
                    ->orLike('phone', $term)
                    ->findAll();
    }
} 