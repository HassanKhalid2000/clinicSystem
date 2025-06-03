<?php

namespace App\Models;

class DoctorModel extends BaseModel
{
    protected $table = 'doctors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'specialization',
        'qualification',
        'experience_years',
        'consultation_fee',
        'available_days',
        'available_time_start',
        'available_time_end',
        'status'
    ];

    protected $validationRules = [
        'user_id' => 'required|is_unique[doctors.user_id,id,{id}]',
        'specialization' => 'required|min_length[3]|max_length[100]',
        'qualification' => 'required|min_length[2]|max_length[100]',
        'experience_years' => 'required|numeric|greater_than[0]',
        'consultation_fee' => 'required|numeric|greater_than[0]',
        'available_days' => 'required',
        'available_time_start' => 'required|valid_time',
        'available_time_end' => 'required|valid_time',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'is_unique' => 'This user is already registered as a doctor'
        ]
    ];

    protected $beforeInsert = ['serializeAvailableDays'];
    protected $beforeUpdate = ['serializeAvailableDays'];
    protected $afterFind = ['unserializeAvailableDays'];

    protected function serializeAvailableDays(array $data)
    {
        if (isset($data['data']['available_days']) && is_array($data['data']['available_days'])) {
            $data['data']['available_days'] = json_encode($data['data']['available_days']);
        }
        return $data;
    }

    protected function unserializeAvailableDays(array $data)
    {
        if (is_array($data)) {
            if (isset($data['available_days'])) {
                $data['available_days'] = json_decode($data['available_days'], true);
            }
            
            if (isset($data['data'])) {
                foreach ($data['data'] as &$row) {
                    if (isset($row['available_days'])) {
                        $row['available_days'] = json_decode($row['available_days'], true);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Get doctor with user details
     */
    public function getDoctorWithUser($id)
    {
        return $this->select('doctors.*, users.first_name, users.last_name, users.email, users.phone')
                    ->join('users', 'users.id = doctors.user_id')
                    ->find($id);
    }

    /**
     * Get all doctors with user details
     */
    public function getAllDoctorsWithUsers()
    {
        return $this->select('doctors.*, users.first_name, users.last_name, users.email, users.phone')
                    ->join('users', 'users.id = doctors.user_id')
                    ->where('doctors.status', 'active')
                    ->findAll();
    }

    /**
     * Check if doctor is available on a specific day and time
     */
    public function isAvailable($doctorId, $day, $time)
    {
        $doctor = $this->find($doctorId);
        if (!$doctor) {
            return false;
        }

        // Check if the day is in available days
        if (!in_array($day, $doctor['available_days'])) {
            return false;
        }

        // Check if the time is within available hours
        $appointmentTime = strtotime($time);
        $startTime = strtotime($doctor['available_time_start']);
        $endTime = strtotime($doctor['available_time_end']);

        return $appointmentTime >= $startTime && $appointmentTime <= $endTime;
    }

    /**
     * Search doctors by name or specialty
     */
    public function search(string $term)
    {
        return $this->select('doctors.*, users.first_name, users.last_name, users.email')
                    ->join('users', 'users.id = doctors.user_id')
                    ->like('users.first_name', $term)
                    ->orLike('users.last_name', $term)
                    ->orLike('doctors.specialization', $term)
                    ->findAll();
    }
} 