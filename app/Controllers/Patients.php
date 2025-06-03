<?php

namespace App\Controllers;

use App\Models\PatientModel;
use App\Models\AppointmentModel;
use App\Models\MedicalRecordModel;

class Patients extends BaseController
{
    protected $patientModel;
    protected $appointmentModel;
    protected $medicalRecordModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->appointmentModel = new AppointmentModel();
        $this->medicalRecordModel = new MedicalRecordModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Patients',
            'patients' => $this->patientModel->findAll()
        ];
        
        return view('patients/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Patient'
        ];
        
        return view('patients/create', $data);
    }

    public function store()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[patients.email]',
            'phone' => 'required|min_length[10]|max_length[15]',
            'date_of_birth' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
            'address' => 'required|min_length[10]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->patientModel->insert([
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'gender' => $this->request->getPost('gender'),
            'address' => $this->request->getPost('address'),
            'blood_type' => $this->request->getPost('blood_type'),
            'allergies' => $this->request->getPost('allergies'),
            'medical_conditions' => $this->request->getPost('medical_conditions')
        ]);

        return redirect()->to('/patients')->with('success', 'Patient added successfully');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Patient',
            'patient' => $this->patientModel->find($id)
        ];

        if (empty($data['patient'])) {
            return redirect()->to('/patients')->with('error', 'Patient not found');
        }

        return view('patients/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => "required|valid_email|is_unique[patients.email,id,$id]",
            'phone' => 'required|min_length[10]|max_length[15]',
            'date_of_birth' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]',
            'address' => 'required|min_length[10]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->patientModel->update($id, [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'gender' => $this->request->getPost('gender'),
            'address' => $this->request->getPost('address'),
            'blood_type' => $this->request->getPost('blood_type'),
            'allergies' => $this->request->getPost('allergies'),
            'medical_conditions' => $this->request->getPost('medical_conditions')
        ]);

        return redirect()->to('/patients')->with('success', 'Patient updated successfully');
    }

    public function delete($id)
    {
        $this->patientModel->delete($id);
        return redirect()->to('/patients')->with('success', 'Patient deleted successfully');
    }

    public function view($id)
    {
        $patient = $this->patientModel->find($id);

        if (empty($patient)) {
            return redirect()->to('/patients')->with('error', 'Patient not found');
        }

        // Get recent appointments
        $appointments = $this->appointmentModel
            ->where('patient_id', $id)
            ->orderBy('appointment_date', 'DESC')
            ->limit(5)
            ->find();

        // Get medical records if user has permission
        $medical_records = [];
        if (in_array(session()->get('role'), ['admin', 'doctor'])) {
            $medical_records = $this->medicalRecordModel
                ->where('patient_id', $id)
                ->orderBy('date', 'DESC')
                ->find();
        }

        $data = [
            'title' => 'Patient Details',
            'patient' => $patient,
            'appointments' => $appointments,
            'medical_records' => $medical_records
        ];

        return view('patients/view', $data);
    }
} 