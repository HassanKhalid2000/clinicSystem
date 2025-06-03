<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Appointment Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Appointment Information</h6>
                    <div>
                        <?php if ($appointment['status'] === 'scheduled'): ?>
                            <a href="<?= site_url('appointments/edit/' . $appointment['id']) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Appointment
                            </a>
                        <?php endif; ?>
                        <a href="<?= site_url('appointments') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Appointment Details</h5>
                            <table class="table">
                                <tr>
                                    <th width="150">Date</th>
                                    <td><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td><?= date('h:i A', strtotime($appointment['appointment_time'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-<?= $appointment['status'] === 'completed' ? 'success' : ($appointment['status'] === 'cancelled' ? 'danger' : 'primary') ?>">
                                            <?= ucfirst($appointment['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Reason</th>
                                    <td><?= nl2br(esc($appointment['reason'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td><?= nl2br(esc($appointment['notes'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Patient Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="150">Name</th>
                                    <td>
                                        <a href="<?= site_url('patients/view/' . $patient['id']) ?>">
                                            <?= esc($patient['first_name']) ?> <?= esc($patient['last_name']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= esc($patient['email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><?= esc($patient['phone']) ?></td>
                                </tr>
                            </table>

                            <h5 class="mb-3 mt-4">Doctor Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="150">Name</th>
                                    <td>
                                        <a href="<?= site_url('doctors/view/' . $doctor['id']) ?>">
                                            <?= esc($doctor['first_name']) ?> <?= esc($doctor['last_name']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Specialty</th>
                                    <td><?= esc($doctor['specialty']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= esc($doctor['email']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if ($appointment['status'] === 'completed'): ?>
                    <!-- Medical Record Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Medical Record</h6>
                                    <?php if (!isset($medical_record)): ?>
                                        <a href="<?= site_url('medical-records/new?appointment_id=' . $appointment['id']) ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Add Medical Record
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($medical_record)): ?>
                                        <table class="table">
                                            <tr>
                                                <th width="150">Diagnosis</th>
                                                <td><?= nl2br(esc($medical_record['diagnosis'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Treatment</th>
                                                <td><?= nl2br(esc($medical_record['treatment'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Prescription</th>
                                                <td><?= nl2br(esc($medical_record['prescription'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Notes</th>
                                                <td><?= nl2br(esc($medical_record['notes'])) ?></td>
                                            </tr>
                                        </table>
                                    <?php else: ?>
                                        <p class="text-muted">No medical record has been added for this appointment yet.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 