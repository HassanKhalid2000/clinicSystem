<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Models\BillingModel;

class Dashboard extends BaseController
{
    protected $appointmentModel;
    protected $patientModel;
    protected $doctorModel;
    protected $billingModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel = new DoctorModel();
        $this->billingModel = new BillingModel();
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $data = [
            'title' => 'Dashboard',
            'user' => [
                'first_name' => session()->get('first_name'),
                'last_name' => session()->get('last_name'),
                'role' => session()->get('role')
            ]
        ];

        // Add statistics based on user role
        if (session()->get('role') === 'admin' || session()->get('role') === 'receptionist') {
            $data['total_patients'] = $this->patientModel->countAllResults();
            $data['total_doctors'] = $this->doctorModel->countAllResults();
            $data['total_appointments'] = $this->appointmentModel->countAllResults();
            $data['total_earnings'] = $this->billingModel->selectSum('amount')->get()->getRow()->amount ?? 0;
        }

        // Get recent appointments
        $data['recent_appointments'] = $this->appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->orderBy('appointment_date', 'DESC')
            ->limit(5)
            ->find();

        // Get upcoming appointments
        $data['upcoming_appointments'] = $this->appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->where('appointment_date >=', date('Y-m-d'))
            ->where('status', 'scheduled')
            ->orderBy('appointment_date', 'ASC')
            ->limit(5)
            ->find();

        // If user is a doctor, filter appointments for this doctor only
        if (session()->get('role') === 'doctor') {
            $doctorId = $this->doctorModel->where('user_id', session()->get('user_id'))->first()['id'];
            $data['total_appointments'] = $this->appointmentModel->where('doctor_id', $doctorId)->countAllResults();
            $data['recent_appointments'] = array_filter($data['recent_appointments'], function($appt) use ($doctorId) {
                return $appt['doctor_id'] == $doctorId;
            });
            $data['upcoming_appointments'] = array_filter($data['upcoming_appointments'], function($appt) use ($doctorId) {
                return $appt['doctor_id'] == $doctorId;
            });
        }

        return view('dashboard/index', $data);
    }
} 