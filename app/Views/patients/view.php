<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Patient Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Patient Details</h1>
        <div>
            <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Patient
            </a>
            <a href="<?= base_url('patients') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Full Name:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= esc($patient['first_name']) . ' ' . esc($patient['last_name']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= esc($patient['email']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Phone:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= esc($patient['phone']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Gender:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= ucfirst(esc($patient['gender'])) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Date of Birth:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= date('F d, Y', strtotime($patient['date_of_birth'])) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Address:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= nl2br(esc($patient['address'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($patient['medical_history'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Medical History</h5>
                </div>
                <div class="card-body">
                    <?= nl2br(esc($patient['medical_history'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Appointments</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($appointments) && !empty($appointments)): ?>
                        <div class="list-group">
                            <?php foreach ($appointments as $appointment): ?>
                                <a href="<?= base_url('appointments/view/' . $appointment['id']) ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></h6>
                                        <small class="text-<?= $appointment['status'] === 'completed' ? 'success' : 
                                                          ($appointment['status'] === 'cancelled' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($appointment['status']) ?>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?= esc($appointment['reason']) ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No recent appointments found.</p>
                    <?php endif; ?>

                    <div class="mt-3">
                        <a href="<?= base_url('appointments/create?patient_id=' . $patient['id']) ?>" 
                           class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-plus"></i> Schedule Appointment
                        </a>
                    </div>
                </div>
            </div>

            <?php if (in_array(session('role'), ['admin', 'doctor'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Medical Records</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($medical_records) && !empty($medical_records)): ?>
                        <div class="list-group">
                            <?php foreach ($medical_records as $record): ?>
                                <a href="<?= base_url('medical-records/view/' . $record['id']) ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= date('M d, Y', strtotime($record['date'])) ?></h6>
                                    </div>
                                    <p class="mb-1"><?= esc($record['diagnosis']) ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No medical records found.</p>
                    <?php endif; ?>

                    <div class="mt-3">
                        <a href="<?= base_url('medical-records/create?patient_id=' . $patient['id']) ?>" 
                           class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-plus"></i> Add Medical Record
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 