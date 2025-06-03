<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= isset($patient) ? 'Edit' : 'Add' ?> Patient<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= isset($patient) ? 'Edit' : 'Add New' ?> Patient</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= isset($patient) ? site_url('patients/update/' . $patient['id']) : site_url('patients/create') ?>" method="post">
                        <?php if (isset($patient)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?= isset($patient) ? esc($patient['first_name']) : old('first_name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?= isset($patient) ? esc($patient['last_name']) : old('last_name') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= isset($patient) ? esc($patient['email']) : old('email') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?= isset($patient) ? esc($patient['phone']) : old('phone') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                           value="<?= isset($patient) ? esc($patient['date_of_birth']) : old('date_of_birth') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?= (isset($patient) && $patient['gender'] == 'male') || old('gender') == 'male' ? 'selected' : '' ?>>Male</option>
                                        <option value="female" <?= (isset($patient) && $patient['gender'] == 'female') || old('gender') == 'female' ? 'selected' : '' ?>>Female</option>
                                        <option value="other" <?= (isset($patient) && $patient['gender'] == 'other') || old('gender') == 'other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= isset($patient) ? esc($patient['address']) : old('address') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="medical_history">Medical History</label>
                            <textarea class="form-control" id="medical_history" name="medical_history" rows="3"><?= isset($patient) ? esc($patient['medical_history']) : old('medical_history') ?></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Patient</button>
                            <a href="<?= site_url('patients') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 