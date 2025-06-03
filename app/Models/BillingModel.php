<?php

namespace App\Models;

class BillingModel extends BaseModel
{
    protected $table = 'billings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'patient_id',
        'appointment_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_date',
        'invoice_number',
        'notes'
    ];

    protected $validationRules = [
        'patient_id' => 'required|numeric|is_not_unique[patients.id]',
        'appointment_id' => 'required|numeric|is_not_unique[appointments.id]',
        'amount' => 'required|numeric|greater_than[0]',
        'payment_method' => 'required|in_list[cash,card,insurance]',
        'payment_status' => 'required|in_list[pending,paid,cancelled]',
        'payment_date' => 'permit_empty|valid_date',
        'invoice_number' => 'required|is_unique[billings.invoice_number,id,{id}]'
    ];

    protected $validationMessages = [
        'patient_id' => [
            'is_not_unique' => 'Patient not found'
        ],
        'appointment_id' => [
            'is_not_unique' => 'Appointment not found'
        ],
        'invoice_number' => [
            'is_unique' => 'This invoice number already exists'
        ]
    ];

    protected $beforeInsert = ['generateInvoiceNumber'];

    protected function generateInvoiceNumber(array $data)
    {
        if (!isset($data['data']['invoice_number'])) {
            $prefix = date('Ymd');
            $lastInvoice = $this->select('invoice_number')
                               ->like('invoice_number', $prefix, 'after')
                               ->orderBy('id', 'DESC')
                               ->first();

            $sequence = '001';
            if ($lastInvoice) {
                $lastSequence = substr($lastInvoice['invoice_number'], -3);
                $sequence = str_pad((int)$lastSequence + 1, 3, '0', STR_PAD_LEFT);
            }

            $data['data']['invoice_number'] = $prefix . $sequence;
        }
        return $data;
    }

    /**
     * Get billing details with related information
     */
    public function getBillingDetails($id)
    {
        return $this->select('billings.*, 
                            patients.first_name as patient_first_name,
                            patients.last_name as patient_last_name,
                            patients.email as patient_email,
                            appointments.appointment_date,
                            appointments.appointment_time,
                            users.first_name as doctor_first_name,
                            users.last_name as doctor_last_name,
                            doctors.specialization as doctor_specialization,
                            doctors.consultation_fee')
                    ->join('patients', 'patients.id = billings.patient_id')
                    ->join('appointments', 'appointments.id = billings.appointment_id')
                    ->join('doctors', 'doctors.id = appointments.doctor_id')
                    ->join('users', 'users.id = doctors.user_id')
                    ->find($id);
    }

    /**
     * Get patient's billing history
     */
    public function getPatientBillings($patientId)
    {
        return $this->select('billings.*, 
                            appointments.appointment_date,
                            appointments.appointment_time,
                            users.first_name as doctor_first_name,
                            users.last_name as doctor_last_name')
                    ->join('appointments', 'appointments.id = billings.appointment_id')
                    ->join('doctors', 'doctors.id = appointments.doctor_id')
                    ->join('users', 'users.id = doctors.user_id')
                    ->where('billings.patient_id', $patientId)
                    ->orderBy('billings.created_at', 'DESC')
                    ->find();
    }

    /**
     * Get pending payments
     */
    public function getPendingPayments()
    {
        return $this->select('billings.*, 
                            patients.first_name as patient_first_name,
                            patients.last_name as patient_last_name,
                            appointments.appointment_date')
                    ->join('patients', 'patients.id = billings.patient_id')
                    ->join('appointments', 'appointments.id = billings.appointment_id')
                    ->where('billings.payment_status', 'pending')
                    ->orderBy('appointments.appointment_date', 'ASC')
                    ->find();
    }

    /**
     * Get billing statistics for a date range
     */
    public function getBillingStats($startDate, $endDate)
    {
        $result = $this->select('
                COUNT(*) as total_bills,
                SUM(CASE WHEN payment_status = "paid" THEN amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN payment_status = "pending" THEN amount ELSE 0 END) as total_pending,
                COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_count,
                COUNT(CASE WHEN payment_status = "pending" THEN 1 END) as pending_count
            ')
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->first();

        return [
            'total_bills' => (int)$result['total_bills'],
            'total_paid' => (float)$result['total_paid'],
            'total_pending' => (float)$result['total_pending'],
            'paid_count' => (int)$result['paid_count'],
            'pending_count' => (int)$result['pending_count']
        ];
    }
} 