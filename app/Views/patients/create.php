<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Add Patient<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Add Patient</h1>
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

            <form action="<?= base_url('patients/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="first_name" 
                               name="first_name" 
                               value="<?= old('first_name') ?>" 
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" 
                               class="form-control" 
                               id="last_name" 
                               name="last_name" 
                               value="<?= old('last_name') ?>" 
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
                               value="<?= old('email') ?>" 
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="<?= old('phone') ?>" 
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
                               value="<?= old('date_of_birth') ?>" 
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="blood_type" class="form-label">Blood Type</label>
                        <select class="form-select" id="blood_type" name="blood_type">
                            <option value="">Select Blood Type</option>
                            <option value="A+" <?= old('blood_type') === 'A+' ? 'selected' : '' ?>>A+</option>
                            <option value="A-" <?= old('blood_type') === 'A-' ? 'selected' : '' ?>>A-</option>
                            <option value="B+" <?= old('blood_type') === 'B+' ? 'selected' : '' ?>>B+</option>
                            <option value="B-" <?= old('blood_type') === 'B-' ? 'selected' : '' ?>>B-</option>
                            <option value="AB+" <?= old('blood_type') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                            <option value="AB-" <?= old('blood_type') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                            <option value="O+" <?= old('blood_type') === 'O+' ? 'selected' : '' ?>>O+</option>
                            <option value="O-" <?= old('blood_type') === 'O-' ? 'selected' : '' ?>>O-</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" 
                              id="address" 
                              name="address" 
                              rows="3" 
                              required><?= old('address') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="allergies" class="form-label">Allergies</label>
                    <textarea class="form-control" 
                              id="allergies" 
                              name="allergies" 
                              rows="3"
                              placeholder="List any known allergies"><?= old('allergies') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="medical_conditions" class="form-label">Medical Conditions</label>
                    <textarea class="form-control" 
                              id="medical_conditions" 
                              name="medical_conditions" 
                              rows="3"
                              placeholder="List any existing medical conditions"><?= old('medical_conditions') ?></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 