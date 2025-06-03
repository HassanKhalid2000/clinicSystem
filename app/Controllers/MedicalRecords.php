<?php

namespace App\Controllers;

use App\Models\MedicalRecordModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;

class MedicalRecords extends BaseController
{
    protected $medicalRecordModel;
    protected $patientModel;
    protected $doctorModel;

    public function __construct()
    {
        $this->medicalRecordModel = new MedicalRecordModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel = new DoctorModel();
    }

    public function index()
    {
        // Only admin and doctors can view all medical records
        if (!in_array(session()->get('role'), ['admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $data = [
            'title' => 'Medical Records',
            'records' => $this->medicalRecordModel->select('medical_records.*, 
                                                          patients.first_name as patient_first_name,
                                                          patients.last_name as patient_last_name,
                                                          users.first_name as doctor_first_name,
                                                          users.last_name as doctor_last_name')
                                                 ->join('patients', 'patients.id = medical_records.patient_id')
                                                 ->join('doctors', 'doctors.id = medical_records.doctor_id')
                                                 ->join('users', 'users.id = doctors.user_id')
                                                 ->orderBy('date', 'DESC')
                                                 ->find()
        ];
        
        return view('medical_records/index', $data);
    }

    public function create()
    {
        // Only admin and doctors can create medical records
        if (!in_array(session()->get('role'), ['admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $patientId = $this->request->getGet('patient_id');

        $data = [
            'title' => 'Add Medical Record',
            'patients' => $this->patientModel->findAll(),
            'doctors' => $this->doctorModel->getAllDoctorsWithUsers(),
            'selected_patient' => $patientId ? $this->patientModel->find($patientId) : null
        ];
        
        return view('medical_records/create', $data);
    }

    public function store()
    {
        // Only admin and doctors can create medical records
        if (!in_array(session()->get('role'), ['admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $rules = [
            'patient_id' => 'required|numeric|is_not_unique[patients.id]',
            'doctor_id' => 'required|numeric|is_not_unique[doctors.id]',
            'date' => 'required|valid_date',
            'diagnosis' => 'required|min_length[5]|max_length[255]',
            'treatment' => 'required|min_length[5]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file uploads
        $attachments = [];
        $files = $this->request->getFiles();
        if (!empty($files['attachments'])) {
            foreach ($files['attachments'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/medical_records', $newName);
                    $attachments[] = [
                        'name' => $file->getClientName(),
                        'path' => $newName
                    ];
                }
            }
        }

        $this->medicalRecordModel->insert([
            'patient_id' => $this->request->getPost('patient_id'),
            'doctor_id' => $this->request->getPost('doctor_id'),
            'date' => $this->request->getPost('date'),
            'diagnosis' => $this->request->getPost('diagnosis'),
            'treatment' => $this->request->getPost('treatment'),
            'prescription' => $this->request->getPost('prescription'),
            'notes' => $this->request->getPost('notes'),
            'attachments' => !empty($attachments) ? json_encode($attachments) : null
        ]);

        return redirect()->to('/medical-records')->with('success', 'Medical record added successfully');
    }

    public function edit($id)
    {
        // Only admin and doctors can edit medical records
        if (!in_array(session()->get('role'), ['admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $record = $this->medicalRecordModel->getMedicalRecordDetails($id);
        
        if (empty($record)) {
            return redirect()->to('/medical-records')->with('error', 'Medical record not found');
        }

        $data = [
            'title' => 'Edit Medical Record',
            'record' => $record,
            'patients' => $this->patientModel->findAll(),
            'doctors' => $this->doctorModel->getAllDoctorsWithUsers()
        ];

        return view('medical_records/edit', $data);
    }

    public function update($id)
    {
        // Only admin and doctors can update medical records
        if (!in_array(session()->get('role'), ['admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $rules = [
            'patient_id' => 'required|numeric|is_not_unique[patients.id]',
            'doctor_id' => 'required|numeric|is_not_unique[doctors.id]',
            'date' => 'required|valid_date',
            'diagnosis' => 'required|min_length[5]|max_length[255]',
            'treatment' => 'required|min_length[5]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get current record to preserve existing attachments
        $currentRecord = $this->medicalRecordModel->find($id);
        $attachments = json_decode($currentRecord['attachments'] ?? '[]', true);

        // Handle new file uploads
        $files = $this->request->getFiles();
        if (!empty($files['attachments'])) {
            foreach ($files['attachments'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/medical_records', $newName);
                    $attachments[] = [
                        'name' => $file->getClientName(),
                        'path' => $newName
                    ];
                }
            }
        }

        // Handle attachment deletions
        $deleteAttachments = $this->request->getPost('delete_attachments');
        if (!empty($deleteAttachments)) {
            foreach ($deleteAttachments as $index) {
                if (isset($attachments[$index])) {
                    // Delete file
                    $filePath = WRITEPATH . 'uploads/medical_records/' . $attachments[$index]['path'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    // Remove from array
                    unset($attachments[$index]);
                }
            }
            // Reindex array
            $attachments = array_values($attachments);
        }

        $this->medicalRecordModel->update($id, [
            'patient_id' => $this->request->getPost('patient_id'),
            'doctor_id' => $this->request->getPost('doctor_id'),
            'date' => $this->request->getPost('date'),
            'diagnosis' => $this->request->getPost('diagnosis'),
            'treatment' => $this->request->getPost('treatment'),
            'prescription' => $this->request->getPost('prescription'),
            'notes' => $this->request->getPost('notes'),
            'attachments' => !empty($attachments) ? json_encode($attachments) : null
        ]);

        return redirect()->to('/medical-records')->with('success', 'Medical record updated successfully');
    }

    public function delete($id)
    {
        // Only admin and doctors can delete medical records
        if (!in_array(session()->get('role'), ['admin', 'doctor'])) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        // Delete attachments
        $record = $this->medicalRecordModel->find($id);
        if (!empty($record['attachments'])) {
            $attachments = json_decode($record['attachments'], true);
            foreach ($attachments as $attachment) {
                $filePath = WRITEPATH . 'uploads/medical_records/' . $attachment['path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $this->medicalRecordModel->delete($id);
        return redirect()->to('/medical-records')->with('success', 'Medical record deleted successfully');
    }

    public function view($id)
    {
        $record = $this->medicalRecordModel->getMedicalRecordDetails($id);
        
        if (empty($record)) {
            return redirect()->to('/medical-records')->with('error', 'Medical record not found');
        }

        // Check if user has permission to view this record
        if (!in_array(session()->get('role'), ['admin', 'doctor']) && 
            session()->get('user_id') != $record['patient_id']) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $data = [
            'title' => 'Medical Record Details',
            'record' => $record
        ];

        return view('medical_records/view', $data);
    }

    public function download($id, $attachmentIndex)
    {
        $record = $this->medicalRecordModel->find($id);
        
        if (empty($record)) {
            return redirect()->to('/medical-records')->with('error', 'Medical record not found');
        }

        // Check if user has permission to download this attachment
        if (!in_array(session()->get('role'), ['admin', 'doctor']) && 
            session()->get('user_id') != $record['patient_id']) {
            return redirect()->to('/dashboard')->with('error', 'Unauthorized access');
        }

        $attachments = json_decode($record['attachments'], true);
        if (!isset($attachments[$attachmentIndex])) {
            return redirect()->to('/medical-records')->with('error', 'Attachment not found');
        }

        $attachment = $attachments[$attachmentIndex];
        $filePath = WRITEPATH . 'uploads/medical_records/' . $attachment['path'];

        if (!file_exists($filePath)) {
            return redirect()->to('/medical-records')->with('error', 'File not found');
        }

        return $this->response->download($filePath, null)
                             ->setFileName($attachment['name']);
    }
} 