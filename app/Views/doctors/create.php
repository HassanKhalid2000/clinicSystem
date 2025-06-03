<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Add Doctor<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Add Doctor</h1>
        <a href="<?= base_url('doctors') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url('doctors/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">Select User</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">Select a user</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['first_name']) . ' ' . esc($user['last_name']) . ' (' . esc($user['email']) . ')' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="specialization" class="form-label">Specialization</label>
                        <input type="text" 
                               class="form-control" 
                               id="specialization" 
                               name="specialization" 
                               value="<?= old('specialization') ?>" 
                               required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="qualification" class="form-label">Qualification</label>
                        <input type="text" 
                               class="form-control" 
                               id="qualification" 
                               name="qualification" 
                               value="<?= old('qualification') ?>" 
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="experience_years" class="form-label">Years of Experience</label>
                        <input type="number" 
                               class="form-control" 
                               id="experience_years" 
                               name="experience_years" 
                               value="<?= old('experience_years') ?>" 
                               min="0" 
                               required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="consultation_fee" class="form-label">Consultation Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="consultation_fee" 
                                   name="consultation_fee" 
                                   value="<?= old('consultation_fee') ?>" 
                                   min="0" 
                                   step="0.01" 
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block">Available Days</label>
                        <div class="btn-group" role="group">
                            <?php 
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            $oldDays = old('available_days') ?? [];
                            foreach ($days as $day): 
                            ?>
                            <input type="checkbox" 
                                   class="btn-check" 
                                   id="day_<?= strtolower($day) ?>" 
                                   name="available_days[]" 
                                   value="<?= $day ?>"
                                   <?= in_array($day, $oldDays) ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="day_<?= strtolower($day) ?>">
                                <?= substr($day, 0, 3) ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="available_time_start" class="form-label">Available Time Start</label>
                        <input type="time" 
                               class="form-control" 
                               id="available_time_start" 
                               name="available_time_start" 
                               value="<?= old('available_time_start') ?>" 
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="available_time_end" class="form-label">Available Time End</label>
                        <input type="time" 
                               class="form-control" 
                               id="available_time_end" 
                               name="available_time_end" 
                               value="<?= old('available_time_end') ?>" 
                               required>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 