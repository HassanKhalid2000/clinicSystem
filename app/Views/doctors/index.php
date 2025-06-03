<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Doctors<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Doctors</h1>
        <?php if (session()->get('role') === 'admin'): ?>
        <a href="<?= base_url('doctors/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Doctor
        </a>
        <?php endif; ?>
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
                            <th>Name</th>
                            <th>Specialization</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Experience</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctors as $doctor): ?>
                        <tr>
                            <td><?= $doctor['id'] ?></td>
                            <td><?= esc($doctor['first_name']) . ' ' . esc($doctor['last_name']) ?></td>
                            <td><?= esc($doctor['specialization']) ?></td>
                            <td><?= esc($doctor['email']) ?></td>
                            <td><?= esc($doctor['phone']) ?></td>
                            <td><?= $doctor['experience_years'] ?> years</td>
                            <td>
                                <span class="badge bg-<?= $doctor['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($doctor['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url('doctors/view/' . $doctor['id']) ?>" 
                                       class="btn btn-sm btn-info" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('doctors/schedule/' . $doctor['id']) ?>" 
                                       class="btn btn-sm btn-secondary" 
                                       title="Schedule">
                                        <i class="fas fa-calendar"></i>
                                    </a>
                                    <?php if (session()->get('role') === 'admin'): ?>
                                    <a href="<?= base_url('doctors/edit/' . $doctor['id']) ?>" 
                                       class="btn btn-sm btn-primary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            title="Delete"
                                            onclick="confirmDelete(<?= $doctor['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($doctors)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No doctors found</td>
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
                Are you sure you want to delete this doctor?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="" id="deleteLink" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    const modal = document.getElementById('deleteModal');
    const deleteLink = document.getElementById('deleteLink');
    deleteLink.href = `<?= base_url('doctors/delete/') ?>/${id}`;
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}
</script>
<?= $this->endSection() ?> 