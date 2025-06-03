<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Doctor Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Doctor Details</h1>
        <div>
            <a href="<?= base_url('doctors/schedule/' . $doctor['id']) ?>" class="btn btn-info">
                <i class="fas fa-calendar"></i> View Schedule
            </a>
            <?php if (session()->get('role') === 'admin'): ?>
            <a href="<?= base_url('doctors/edit/' . $doctor['id']) ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Doctor
            </a>
            <?php endif; ?>
            <a href="<?= base_url('doctors') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Doctor Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Name</dt>
                                <dd class="col-sm-8"><?= esc($doctor['first_name']) . ' ' . esc($doctor['last_name']) ?></dd>

                                <dt class="col-sm-4">Email</dt>
                                <dd class="col-sm-8"><?= esc($doctor['email']) ?></dd>

                                <dt class="col-sm-4">Phone</dt>
                                <dd class="col-sm-8"><?= esc($doctor['phone']) ?></dd>

                                <dt class="col-sm-4">Specialization</dt>
                                <dd class="col-sm-8"><?= esc($doctor['specialization']) ?></dd>

                                <dt class="col-sm-4">Qualification</dt>
                                <dd class="col-sm-8"><?= esc($doctor['qualification']) ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Experience</dt>
                                <dd class="col-sm-8"><?= $doctor['experience_years'] ?> years</dd>

                                <dt class="col-sm-4">Fee</dt>
                                <dd class="col-sm-8">$<?= number_format($doctor['consultation_fee'], 2) ?></dd>

                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-<?= $doctor['status'] === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($doctor['status']) ?>
                                    </span>
                                </dd>

                                <dt class="col-sm-4">Available Days</dt>
                                <dd class="col-sm-8">
                                    <?php 
                                    $availableDays = json_decode($doctor['available_days']);
                                    foreach ($availableDays as $day): 
                                    ?>
                                    <span class="badge bg-primary me-1"><?= substr($day, 0, 3) ?></span>
                                    <?php endforeach; ?>
                                </dd>

                                <dt class="col-sm-4">Time</dt>
                                <dd class="col-sm-8">
                                    <?= date('h:i A', strtotime($doctor['available_time_start'])) ?> - 
                                    <?= date('h:i A', strtotime($doctor['available_time_end'])) ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upcoming Appointments</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($appointments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></td>
                                    <td><?= date('h:i A', strtotime($appointment['appointment_time'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('patients/view/' . $appointment['patient_id']) ?>">
                                            <?= esc($appointment['patient_name']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $appointment['status'] === 'scheduled' ? 'primary' : 
                                                              ($appointment['status'] === 'completed' ? 'success' : 'danger') ?>">
                                            <?= ucfirst($appointment['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('appointments/view/' . $appointment['id']) ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted mb-0">No upcoming appointments</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Schedule for Today</h5>
                </div>
                <div class="card-body">
                    <?php 
                    $today = date('l');
                    $availableDays = json_decode($doctor['available_days']);
                    if (in_array($today, $availableDays)):
                    ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Available today from 
                        <?= date('h:i A', strtotime($doctor['available_time_start'])) ?> to 
                        <?= date('h:i A', strtotime($doctor['available_time_end'])) ?>
                    </div>
                    <a href="<?= base_url('appointments/create?doctor_id=' . $doctor['id']) ?>" 
                       class="btn btn-primary w-100">
                        <i class="fas fa-calendar-plus"></i> Schedule Appointment
                    </a>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle"></i> Not available today
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 