<?php

namespace App\Models;

class AppointmentModel extends BaseModel
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'reason',
        'status',
        'notes'
    ];

    protected $validationRules = [
        'patient_id' => 'required|numeric|is_not_unique[patients.id]',
        'doctor_id' => 'required|numeric|is_not_unique[doctors.id]',
        'appointment_date' => 'required|valid_date',
        'appointment_time' => 'required|valid_time',
        'reason' => 'required|min_length[5]|max_length[255]',
        'status' => 'required|in_list[scheduled,completed,cancelled]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'is_not_unique' => 'Patient not found'
        ],
        'doctor_id' => [
            'is_not_unique' => 'Doctor not found'
        ]
    ];

    /**
     * Get appointment with patient and doctor details
     */
    public function getAppointmentDetails($id)
    {
        return $this->select('appointments.*, 
                            patients.first_name as patient_first_name, 
                            patients.last_name as patient_last_name,
                            patients.phone as patient_phone,
                            patients.email as patient_email,
                            users.first_name as doctor_first_name,
                            users.last_name as doctor_last_name,
                            doctors.specialization as doctor_specialization')
                    ->join('patients', 'patients.id = appointments.patient_id')
                    ->join('doctors', 'doctors.id = appointments.doctor_id')
                    ->join('users', 'users.id = doctors.user_id')
                    ->find($id);
    }

    /**
     * Get appointments for a specific doctor
     */
    public function getDoctorAppointments($doctorId, $status = null)
    {
        $builder = $this->select('appointments.*, 
                                patients.first_name as patient_first_name, 
                                patients.last_name as patient_last_name')
                        ->join('patients', 'patients.id = appointments.patient_id')
                        ->where('appointments.doctor_id', $doctorId);

        if ($status) {
            $builder->where('appointments.status', $status);
        }

        return $builder->orderBy('appointment_date', 'DESC')
                      ->orderBy('appointment_time', 'ASC')
                      ->find();
    }

    /**
     * Get appointments for a specific patient
     */
    public function getPatientAppointments($patientId, $status = null)
    {
        $builder = $this->select('appointments.*, 
                                users.first_name as doctor_first_name, 
                                users.last_name as doctor_last_name,
                                doctors.specialization as doctor_specialization')
                        ->join('doctors', 'doctors.id = appointments.doctor_id')
                        ->join('users', 'users.id = doctors.user_id')
                        ->where('appointments.patient_id', $patientId);

        if ($status) {
            $builder->where('appointments.status', $status);
        }

        return $builder->orderBy('appointment_date', 'DESC')
                      ->orderBy('appointment_time', 'ASC')
                      ->find();
    }

    /**
     * Check if the time slot is available for the doctor
     */
    public function isTimeSlotAvailable($doctorId, $date, $time, $excludeAppointmentId = null)
    {
        $builder = $this->where('doctor_id', $doctorId)
                       ->where('appointment_date', $date)
                       ->where('appointment_time', $time)
                       ->where('status !=', 'cancelled');

        if ($excludeAppointmentId) {
            $builder->where('id !=', $excludeAppointmentId);
        }

        return $builder->countAllResults() === 0;
    }

    /**
     * Get upcoming appointments
     */
    public function getUpcomingAppointments($limit = null)
    {
        $builder = $this->select('appointments.*, 
                                patients.first_name as patient_first_name, 
                                patients.last_name as patient_last_name,
                                users.first_name as doctor_first_name,
                                users.last_name as doctor_last_name')
                        ->join('patients', 'patients.id = appointments.patient_id')
                        ->join('doctors', 'doctors.id = appointments.doctor_id')
                        ->join('users', 'users.id = doctors.user_id')
                        ->where('appointment_date >=', date('Y-m-d'))
                        ->where('status', 'scheduled')
                        ->orderBy('appointment_date', 'ASC')
                        ->orderBy('appointment_time', 'ASC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->find();
    }
} 