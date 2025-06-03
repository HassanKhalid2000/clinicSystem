<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Models\BillingModel;

class Appointments extends BaseController
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
        $data = [
            'title' => 'Appointments',
            'appointments' => $this->appointmentModel->getUpcomingAppointments()
        ];
        
        return view('appointments/index', $data);
    }

    public function create()
    {
        $patientId = $this->request->getGet('patient_id');
        $doctorId = $this->request->getGet('doctor_id');

        $data = [
            'title' => 'Schedule Appointment',
            'patients' => $this->patientModel->findAll(),
            'doctors' => $this->doctorModel->getAllDoctorsWithUsers(),
            'selected_patient' => $patientId ? $this->patientModel->find($patientId) : null,
            'selected_doctor' => $doctorId ? $this->doctorModel->getDoctorWithUser($doctorId) : null
        ];
        
        return view('appointments/create', $data);
    }

    public function store()
    {
        $rules = [
            'patient_id' => 'required|numeric|is_not_unique[patients.id]',
            'doctor_id' => 'required|numeric|is_not_unique[doctors.id]',
            'appointment_date' => 'required|valid_date',
            'appointment_time' => 'required|valid_time',
            'reason' => 'required|min_length[5]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $doctorId = $this->request->getPost('doctor_id');
        $date = $this->request->getPost('appointment_date');
        $time = $this->request->getPost('appointment_time');

        // Check if doctor is available
        $doctor = $this->doctorModel->find($doctorId);
        $dayOfWeek = date('l', strtotime($date));
        
        if (!in_array($dayOfWeek, json_decode($doctor['available_days']))) {
            return redirect()->back()->withInput()->with('error', 'Doctor is not available on this day');
        }

        // Check if time slot is available
        if (!$this->appointmentModel->isTimeSlotAvailable($doctorId, $date, $time)) {
            return redirect()->back()->withInput()->with('error', 'This time slot is already booked');
        }

        // Create appointment
        $appointmentId = $this->appointmentModel->insert([
            'patient_id' => $this->request->getPost('patient_id'),
            'doctor_id' => $doctorId,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'reason' => $this->request->getPost('reason'),
            'status' => 'scheduled',
            'notes' => $this->request->getPost('notes')
        ]);

        // Create billing record
        $this->billingModel->insert([
            'patient_id' => $this->request->getPost('patient_id'),
            'appointment_id' => $appointmentId,
            'amount' => $doctor['consultation_fee'],
            'payment_status' => 'pending'
        ]);

        return redirect()->to('/appointments')->with('success', 'Appointment scheduled successfully');
    }

    public function edit($id)
    {
        $appointment = $this->appointmentModel->getAppointmentDetails($id);
        
        if (empty($appointment)) {
            return redirect()->to('/appointments')->with('error', 'Appointment not found');
        }

        $data = [
            'title' => 'Edit Appointment',
            'appointment' => $appointment,
            'patients' => $this->patientModel->findAll(),
            'doctors' => $this->doctorModel->getAllDoctorsWithUsers()
        ];

        return view('appointments/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'patient_id' => 'required|numeric|is_not_unique[patients.id]',
            'doctor_id' => 'required|numeric|is_not_unique[doctors.id]',
            'appointment_date' => 'required|valid_date',
            'appointment_time' => 'required|valid_time',
            'reason' => 'required|min_length[5]|max_length[255]',
            'status' => 'required|in_list[scheduled,completed,cancelled]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $doctorId = $this->request->getPost('doctor_id');
        $date = $this->request->getPost('appointment_date');
        $time = $this->request->getPost('appointment_time');

        // Check if doctor is available (only if date/time/doctor changed)
        $currentAppointment = $this->appointmentModel->find($id);
        if ($doctorId != $currentAppointment['doctor_id'] || 
            $date != $currentAppointment['appointment_date'] || 
            $time != $currentAppointment['appointment_time']) {
            
            $doctor = $this->doctorModel->find($doctorId);
            $dayOfWeek = date('l', strtotime($date));
            
            if (!in_array($dayOfWeek, json_decode($doctor['available_days']))) {
                return redirect()->back()->withInput()->with('error', 'Doctor is not available on this day');
            }

            // Check if time slot is available
            if (!$this->appointmentModel->isTimeSlotAvailable($doctorId, $date, $time, $id)) {
                return redirect()->back()->withInput()->with('error', 'This time slot is already booked');
            }
        }

        $this->appointmentModel->update($id, [
            'patient_id' => $this->request->getPost('patient_id'),
            'doctor_id' => $doctorId,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'reason' => $this->request->getPost('reason'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ]);

        // Update billing status if appointment is cancelled
        if ($this->request->getPost('status') === 'cancelled') {
            $this->billingModel->where('appointment_id', $id)
                              ->set(['payment_status' => 'cancelled'])
                              ->update();
        }

        return redirect()->to('/appointments')->with('success', 'Appointment updated successfully');
    }

    public function delete($id)
    {
        $this->appointmentModel->delete($id);
        return redirect()->to('/appointments')->with('success', 'Appointment deleted successfully');
    }

    public function view($id)
    {
        $appointment = $this->appointmentModel->getAppointmentDetails($id);
        
        if (empty($appointment)) {
            return redirect()->to('/appointments')->with('error', 'Appointment not found');
        }

        // Get billing information
        $billing = $this->billingModel->where('appointment_id', $id)->first();

        $data = [
            'title' => 'Appointment Details',
            'appointment' => $appointment,
            'billing' => $billing
        ];

        return view('appointments/view', $data);
    }

    public function calendar()
    {
        $data = [
            'title' => 'Appointment Calendar',
            'doctors' => $this->doctorModel->getAllDoctorsWithUsers()
        ];

        return view('appointments/calendar', $data);
    }

    public function getCalendarData()
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $doctorId = $this->request->getGet('doctor_id');

        $builder = $this->appointmentModel->select('
                appointments.id,
                CONCAT(patients.first_name, " ", patients.last_name) as title,
                CONCAT(appointment_date, " ", appointment_time) as start,
                appointments.status,
                appointments.reason
            ')
            ->join('patients', 'patients.id = appointments.patient_id')
            ->where('appointment_date >=', $start)
            ->where('appointment_date <=', $end);

        if ($doctorId) {
            $builder->where('doctor_id', $doctorId);
        }

        $appointments = $builder->find();

        // Format appointments for FullCalendar
        $events = [];
        foreach ($appointments as $appointment) {
            $color = '';
            switch ($appointment['status']) {
                case 'scheduled':
                    $color = '#3788d8'; // blue
                    break;
                case 'completed':
                    $color = '#28a745'; // green
                    break;
                case 'cancelled':
                    $color = '#dc3545'; // red
                    break;
            }

            $events[] = [
                'id' => $appointment['id'],
                'title' => $appointment['title'],
                'start' => $appointment['start'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $appointment['status'],
                    'reason' => $appointment['reason']
                ]
            ];
        }

        return $this->response->setJSON($events);
    }
} 