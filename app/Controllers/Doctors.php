<?php

namespace App\Controllers;

use App\Models\DoctorModel;
use App\Models\UserModel;
use App\Models\AppointmentModel;

class Doctors extends BaseController
{
    protected $doctorModel;
    protected $userModel;
    protected $appointmentModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
        $this->userModel = new UserModel();
        $this->appointmentModel = new AppointmentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Doctors',
            'doctors' => $this->doctorModel->getAllDoctorsWithUsers()
        ];
        
        return view('doctors/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add Doctor',
            'users' => $this->userModel->where('role', 'doctor')
                                     ->whereNotIn('id', function($builder) {
                                         $builder->select('user_id')->from('doctors');
                                     })
                                     ->findAll()
        ];
        
        return view('doctors/create', $data);
    }

    public function store()
    {
        $rules = [
            'user_id' => 'required|is_unique[doctors.user_id]',
            'specialization' => 'required|min_length[3]|max_length[100]',
            'qualification' => 'required|min_length[2]|max_length[100]',
            'experience_years' => 'required|numeric|greater_than[0]',
            'consultation_fee' => 'required|numeric|greater_than[0]',
            'available_days' => 'required',
            'available_time_start' => 'required|valid_time',
            'available_time_end' => 'required|valid_time'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Convert available_days array to JSON
        $availableDays = $this->request->getPost('available_days');
        if (is_array($availableDays)) {
            $availableDays = json_encode($availableDays);
        }

        $this->doctorModel->insert([
            'user_id' => $this->request->getPost('user_id'),
            'specialization' => $this->request->getPost('specialization'),
            'qualification' => $this->request->getPost('qualification'),
            'experience_years' => $this->request->getPost('experience_years'),
            'consultation_fee' => $this->request->getPost('consultation_fee'),
            'available_days' => $availableDays,
            'available_time_start' => $this->request->getPost('available_time_start'),
            'available_time_end' => $this->request->getPost('available_time_end'),
            'status' => 'active'
        ]);

        return redirect()->to('/doctors')->with('success', 'Doctor added successfully');
    }

    public function edit($id)
    {
        $doctor = $this->doctorModel->getDoctorWithUser($id);
        
        if (empty($doctor)) {
            return redirect()->to('/doctors')->with('error', 'Doctor not found');
        }

        $data = [
            'title' => 'Edit Doctor',
            'doctor' => $doctor
        ];

        return view('doctors/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'specialization' => 'required|min_length[3]|max_length[100]',
            'qualification' => 'required|min_length[2]|max_length[100]',
            'experience_years' => 'required|numeric|greater_than[0]',
            'consultation_fee' => 'required|numeric|greater_than[0]',
            'available_days' => 'required',
            'available_time_start' => 'required|valid_time',
            'available_time_end' => 'required|valid_time'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Convert available_days array to JSON
        $availableDays = $this->request->getPost('available_days');
        if (is_array($availableDays)) {
            $availableDays = json_encode($availableDays);
        }

        $this->doctorModel->update($id, [
            'specialization' => $this->request->getPost('specialization'),
            'qualification' => $this->request->getPost('qualification'),
            'experience_years' => $this->request->getPost('experience_years'),
            'consultation_fee' => $this->request->getPost('consultation_fee'),
            'available_days' => $availableDays,
            'available_time_start' => $this->request->getPost('available_time_start'),
            'available_time_end' => $this->request->getPost('available_time_end')
        ]);

        return redirect()->to('/doctors')->with('success', 'Doctor updated successfully');
    }

    public function delete($id)
    {
        $this->doctorModel->delete($id);
        return redirect()->to('/doctors')->with('success', 'Doctor deleted successfully');
    }

    public function view($id)
    {
        $doctor = $this->doctorModel->getDoctorWithUser($id);
        
        if (empty($doctor)) {
            return redirect()->to('/doctors')->with('error', 'Doctor not found');
        }

        // Get upcoming appointments
        $appointments = $this->appointmentModel
            ->where('doctor_id', $id)
            ->where('appointment_date >=', date('Y-m-d'))
            ->where('status', 'scheduled')
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->limit(5)
            ->find();

        $data = [
            'title' => 'Doctor Details',
            'doctor' => $doctor,
            'appointments' => $appointments
        ];

        return view('doctors/view', $data);
    }

    public function schedule($id)
    {
        $doctor = $this->doctorModel->getDoctorWithUser($id);
        
        if (empty($doctor)) {
            return redirect()->to('/doctors')->with('error', 'Doctor not found');
        }

        // Get all appointments for the next 7 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+7 days'));

        $appointments = $this->appointmentModel
            ->where('doctor_id', $id)
            ->where('appointment_date >=', $startDate)
            ->where('appointment_date <=', $endDate)
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->find();

        $data = [
            'title' => 'Doctor Schedule',
            'doctor' => $doctor,
            'appointments' => $appointments,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('doctors/schedule', $data);
    }
} 