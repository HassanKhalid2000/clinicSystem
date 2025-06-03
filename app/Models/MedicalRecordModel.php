<?php

namespace App\Models;

class MedicalRecordModel extends BaseModel
{
    protected $table = 'medical_records';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'patient_id',
        'doctor_id',
        'date',
        'diagnosis',
        'treatment',
        'prescription',
        'notes',
        'attachments'
    ];

    protected $validationRules = [
        'patient_id' => 'required|numeric|is_not_unique[patients.id]',
        'doctor_id' => 'required|numeric|is_not_unique[doctors.id]',
        'date' => 'required|valid_date',
        'diagnosis' => 'required|min_length[5]|max_length[255]',
        'treatment' => 'required|min_length[5]|max_length[255]',
        'prescription' => 'permit_empty|max_length[1000]',
        'notes' => 'permit_empty|max_length[1000]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'is_not_unique' => 'Patient not found'
        ],
        'doctor_id' => [
            'is_not_unique' => 'Doctor not found'
        ]
    ];

    protected $beforeInsert = ['serializeAttachments'];
    protected $beforeUpdate = ['serializeAttachments'];
    protected $afterFind = ['unserializeAttachments'];

    protected function serializeAttachments(array $data)
    {
        if (isset($data['data']['attachments']) && is_array($data['data']['attachments'])) {
            $data['data']['attachments'] = json_encode($data['data']['attachments']);
        }
        return $data;
    }

    protected function unserializeAttachments(array $data)
    {
        if (is_array($data)) {
            if (isset($data['attachments'])) {
                $data['attachments'] = json_decode($data['attachments'], true);
            }
            
            if (isset($data['data'])) {
                foreach ($data['data'] as &$row) {
                    if (isset($row['attachments'])) {
                        $row['attachments'] = json_decode($row['attachments'], true);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Get medical record with patient and doctor details
     */
    public function getMedicalRecordDetails($id)
    {
        return $this->select('medical_records.*, 
                            patients.first_name as patient_first_name, 
                            patients.last_name as patient_last_name,
                            users.first_name as doctor_first_name,
                            users.last_name as doctor_last_name,
                            doctors.specialization as doctor_specialization')
                    ->join('patients', 'patients.id = medical_records.patient_id')
                    ->join('doctors', 'doctors.id = medical_records.doctor_id')
                    ->join('users', 'users.id = doctors.user_id')
                    ->find($id);
    }

    /**
     * Get medical records for a specific patient
     */
    public function getPatientMedicalRecords($patientId)
    {
        return $this->select('medical_records.*, 
                            users.first_name as doctor_first_name,
                            users.last_name as doctor_last_name,
                            doctors.specialization as doctor_specialization')
                    ->join('doctors', 'doctors.id = medical_records.doctor_id')
                    ->join('users', 'users.id = doctors.user_id')
                    ->where('medical_records.patient_id', $patientId)
                    ->orderBy('date', 'DESC')
                    ->find();
    }

    /**
     * Get medical records created by a specific doctor
     */
    public function getDoctorMedicalRecords($doctorId)
    {
        return $this->select('medical_records.*, 
                            patients.first_name as patient_first_name,
                            patients.last_name as patient_last_name')
                    ->join('patients', 'patients.id = medical_records.patient_id')
                    ->where('medical_records.doctor_id', $doctorId)
                    ->orderBy('date', 'DESC')
                    ->find();
    }
} 