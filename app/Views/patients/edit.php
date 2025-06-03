<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit Patient<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Patient</h1>
        <a href="<?= base_url('patients') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (session('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <form action="<?= base_url('patients/update/' . $patient['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="first_name" 
                               name="first_name" 
                               value="<?= old('first_name', $patient['first_name']) ?>" 
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="last_name" 
                               name="last_name" 
                               value="<?= old('last_name', $patient['last_name']) ?>" 
                               required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?= old('email', $patient['email']) ?>" 
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone', $patient['phone']) ?>" 
                               required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_of_birth" 
                               name="date_of_birth" 
                               value="<?= old('date_of_birth', $patient['date_of_birth']) ?>" 
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?= old('gender', $patient['gender']) === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= old('gender', $patient['gender']) === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= old('gender', $patient['gender']) === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" 
                              id="address" 
                              name="address" 
                              rows="3" 
                              required><?= old('address', $patient['address']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="medical_history" class="form-label">Medical History</label>
                    <textarea class="form-control" 
                              id="medical_history" 
                              name="medical_history" 
                              rows="4"><?= old('medical_history', $patient['medical_history']) ?></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 