<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Appointments<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Appointments</h1>
        <div>
            <a href="<?= base_url('appointments/calendar') ?>" class="btn btn-info">
                <i class="fas fa-calendar"></i> Calendar View
            </a>
            <a href="<?= base_url('appointments/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Appointment
            </a>
        </div>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= $appointment['id'] ?></td>
                            <td><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></td>
                            <td><?= date('h:i A', strtotime($appointment['appointment_time'])) ?></td>
                            <td>
                                <a href="<?= base_url('patients/view/' . $appointment['patient_id']) ?>">
                                    <?= esc($appointment['patient_name']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= base_url('doctors/view/' . $appointment['doctor_id']) ?>">
                                    <?= esc($appointment['doctor_name']) ?>
                                </a>
                            </td>
                            <td><?= esc(substr($appointment['reason'], 0, 50)) . (strlen($appointment['reason']) > 50 ? '...' : '') ?></td>
                            <td>
                                <span class="badge bg-<?= $appointment['status'] === 'scheduled' ? 'primary' : 
                                                      ($appointment['status'] === 'completed' ? 'success' : 'danger') ?>">
                                    <?= ucfirst($appointment['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url('appointments/view/' . $appointment['id']) ?>" 
                                       class="btn btn-sm btn-info" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($appointment['status'] === 'scheduled'): ?>
                                    <a href="<?= base_url('appointments/edit/' . $appointment['id']) ?>" 
                                       class="btn btn-sm btn-primary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            title="Cancel"
                                            onclick="confirmCancel(<?= $appointment['id'] ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No appointments found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this appointment?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <a href="" id="cancelLink" class="btn btn-danger">Yes, Cancel</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmCancel(id) {
    const modal = document.getElementById('cancelModal');
    const cancelLink = document.getElementById('cancelLink');
    cancelLink.href = `<?= base_url('appointments/delete/') ?>/${id}`;
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}
</script>
<?= $this->endSection() ?> 