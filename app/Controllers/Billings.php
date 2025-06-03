<?php

namespace App\Controllers;

use App\Models\BillingModel;
use App\Models\PatientModel;
use App\Models\AppointmentModel;

class Billings extends BaseController
{
    protected $billingModel;
    protected $patientModel;
    protected $appointmentModel;

    public function __construct()
    {
        $this->billingModel = new BillingModel();
        $this->patientModel = new PatientModel();
        $this->appointmentModel = new AppointmentModel();
    }

    public function index()
    {
        // Only admin and staff can view all billings
        if (!in_array(session()->get('role'), ['admin', 'staff'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $data = [
            'title' => 'Billings',
            'billings' => $this->billingModel->select('billings.*, 
                                                      patients.first_name as patient_first_name,
                                                      patients.last_name as patient_last_name,
                                                      appointments.appointment_date')
                                           ->join('patients', 'patients.id = billings.patient_id')
                                           ->join('appointments', 'appointments.id = billings.appointment_id')
                                           ->orderBy('created_at', 'DESC')
                                           ->find()
        ];
        
        return view('billings/index', $data);
    }

    public function create()
    {
        // Only admin and staff can create billings
        if (!in_array(session()->get('role'), ['admin', 'staff'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $appointmentId = $this->request->getGet('appointment_id');

        if ($appointmentId) {
            $appointment = $this->appointmentModel->getAppointmentDetails($appointmentId);
            if (empty($appointment)) {
                return redirect()->to('/billings')->with('error', 'Appointment not found');
            }
        }

        $data = [
            'title' => 'Create Bill',
            'patients' => $this->patientModel->findAll(),
            'appointments' => $appointmentId ? [$appointment] : $this->appointmentModel->where('status', 'completed')
                                                                                     ->orderBy('appointment_date', 'DESC')
                                                                                     ->find()
        ];
        
        return view('billings/create', $data);
    }

    public function store()
    {
        // Only admin and staff can create billings
        if (!in_array(session()->get('role'), ['admin', 'staff'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $rules = [
            'patient_id' => 'required|numeric|is_not_unique[patients.id]',
            'appointment_id' => 'required|numeric|is_not_unique[appointments.id]',
            'amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[cash,card,insurance]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if billing already exists for this appointment
        $existingBilling = $this->billingModel->where('appointment_id', $this->request->getPost('appointment_id'))
                                             ->first();
        if ($existingBilling) {
            return redirect()->back()->withInput()->with('error', 'A billing record already exists for this appointment');
        }

        $this->billingModel->insert([
            'patient_id' => $this->request->getPost('patient_id'),
            'appointment_id' => $this->request->getPost('appointment_id'),
            'amount' => $this->request->getPost('amount'),
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_status' => 'pending',
            'notes' => $this->request->getPost('notes')
        ]);

        return redirect()->to('/billings')->with('success', 'Bill created successfully');
    }

    public function edit($id)
    {
        // Only admin and staff can edit billings
        if (!in_array(session()->get('role'), ['admin', 'staff'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $billing = $this->billingModel->getBillingDetails($id);
        
        if (empty($billing)) {
            return redirect()->to('/billings')->with('error', 'Bill not found');
        }

        $data = [
            'title' => 'Edit Bill',
            'billing' => $billing
        ];

        return view('billings/edit', $data);
    }

    public function update($id)
    {
        // Only admin and staff can update billings
        if (!in_array(session()->get('role'), ['admin', 'staff'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $rules = [
            'amount' => 'required|numeric|greater_than[0]',
            'payment_method' => 'required|in_list[cash,card,insurance]',
            'payment_status' => 'required|in_list[pending,paid,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'amount' => $this->request->getPost('amount'),
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_status' => $this->request->getPost('payment_status'),
            'notes' => $this->request->getPost('notes')
        ];

        // Set payment date if status is changed to paid
        if ($this->request->getPost('payment_status') === 'paid') {
            $updateData['payment_date'] = date('Y-m-d H:i:s');
        }

        $this->billingModel->update($id, $updateData);

        return redirect()->to('/billings')->with('success', 'Bill updated successfully');
    }

    public function delete($id)
    {
        // Only admin can delete billings
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $this->billingModel->delete($id);
        return redirect()->to('/billings')->with('success', 'Bill deleted successfully');
    }

    public function view($id)
    {
        $billing = $this->billingModel->getBillingDetails($id);
        
        if (empty($billing)) {
            return redirect()->to('/billings')->with('error', 'Bill not found');
        }

        // Check if user has permission to view this bill
        if (!in_array(session()->get('role'), ['admin', 'staff']) && 
            session()->get('user_id') != $billing['patient_id']) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $data = [
            'title' => 'Bill Details',
            'billing' => $billing
        ];

        return view('billings/view', $data);
    }

    public function print($id)
    {
        $billing = $this->billingModel->getBillingDetails($id);
        
        if (empty($billing)) {
            return redirect()->to('/billings')->with('error', 'Bill not found');
        }

        // Check if user has permission to print this bill
        if (!in_array(session()->get('role'), ['admin', 'staff']) && 
            session()->get('user_id') != $billing['patient_id']) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $data = [
            'title' => 'Print Bill',
            'billing' => $billing
        ];

        return view('billings/print', $data);
    }

    public function report()
    {
        // Only admin and staff can view billing reports
        if (!in_array(session()->get('role'), ['admin', 'staff'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $stats = $this->billingModel->getBillingStats($startDate, $endDate);

        $data = [
            'title' => 'Billing Report',
            'stats' => $stats,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        return view('billings/report', $data);
    }
} 