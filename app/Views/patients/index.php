<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Patients<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Patients</h1>
        <a href="<?= base_url('patients/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Patient
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?= $patient['id'] ?></td>
                            <td><?= esc($patient['first_name']) . ' ' . esc($patient['last_name']) ?></td>
                            <td><?= esc($patient['email']) ?></td>
                            <td><?= esc($patient['phone']) ?></td>
                            <td><?= ucfirst(esc($patient['gender'])) ?></td>
                            <td><?= date('M d, Y', strtotime($patient['date_of_birth'])) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url('patients/view/' . $patient['id']) ?>" 
                                       class="btn btn-sm btn-info" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('patients/edit/' . $patient['id']) ?>" 
                                       class="btn btn-sm btn-primary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            title="Delete"
                                            onclick="confirmDelete(<?= $patient['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No patients found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this patient?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="" method="post" class="d-inline">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    const modal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `<?= base_url('patients/delete/') ?>/${id}`;
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}
</script>
<?= $this->endSection() ?> 