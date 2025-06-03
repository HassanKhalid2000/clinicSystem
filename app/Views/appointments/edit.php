<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit Appointment<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Edit Appointment</h1>
        <a href="<?= base_url('appointments') ?>" class="btn btn-secondary">
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
            <form action="<?= base_url('appointments/update/' . $appointment['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="patient_id" class="form-label">Patient</label>
                        <select name="patient_id" id="patient_id" class="form-select select2" required>
                            <option value="">Select a patient</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?= $patient['id'] ?>" 
                                        <?= old('patient_id', $appointment['patient_id']) == $patient['id'] ? 'selected' : '' ?>>
                                    <?= esc($patient['first_name']) . ' ' . esc($patient['last_name']) ?> 
                                    (<?= esc($patient['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="doctor_id" class="form-label">Doctor</label>
                        <select name="doctor_id" id="doctor_id" class="form-select select2" required>
                            <option value="">Select a doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>" 
                                        data-days="<?= htmlspecialchars($doctor['available_days']) ?>"
                                        data-start="<?= $doctor['available_time_start'] ?>"
                                        data-end="<?= $doctor['available_time_end'] ?>"
                                        <?= old('doctor_id', $appointment['doctor_id']) == $doctor['id'] ? 'selected' : '' ?>>
                                    <?= esc($doctor['first_name']) . ' ' . esc($doctor['last_name']) ?> 
                                    (<?= esc($doctor['specialization']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="appointment_date" class="form-label">Date</label>
                        <input type="date" 
                               class="form-control" 
                               id="appointment_date" 
                               name="appointment_date" 
                               value="<?= old('appointment_date', $appointment['appointment_date']) ?>" 
                               min="<?= date('Y-m-d') ?>" 
                               required>
                        <div id="dateError" class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="appointment_time" class="form-label">Time</label>
                        <input type="time" 
                               class="form-control" 
                               id="appointment_time" 
                               name="appointment_time" 
                               value="<?= old('appointment_time', $appointment['appointment_time']) ?>" 
                               required>
                        <div id="timeError" class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Visit</label>
                    <textarea class="form-control" 
                              id="reason" 
                              name="reason" 
                              rows="3" 
                              required><?= old('reason', $appointment['reason']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control" 
                              id="notes" 
                              name="notes" 
                              rows="2"><?= old('notes', $appointment['notes']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="scheduled" <?= old('status', $appointment['status']) === 'scheduled' ? 'selected' : '' ?>>
                            Scheduled
                        </option>
                        <option value="completed" <?= old('status', $appointment['status']) === 'completed' ? 'selected' : '' ?>>
                            Completed
                        </option>
                        <option value="cancelled" <?= old('status', $appointment['status']) === 'cancelled' ? 'selected' : '' ?>>
                            Cancelled
                        </option>
                    </select>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5'
    });

    // Handle doctor availability
    function checkDoctorAvailability() {
        const doctorSelect = document.getElementById('doctor_id');
        const dateInput = document.getElementById('appointment_date');
        const timeInput = document.getElementById('appointment_time');
        const dateError = document.getElementById('dateError');
        const timeError = document.getElementById('timeError');

        if (doctorSelect.value && dateInput.value) {
            const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];
            const availableDays = JSON.parse(selectedOption.dataset.days);
            const startTime = selectedOption.dataset.start;
            const endTime = selectedOption.dataset.end;

            // Check if selected date is an available day
            const selectedDate = new Date(dateInput.value);
            const dayOfWeek = selectedDate.toLocaleString('en-US', { weekday: 'long' });
            
            if (!availableDays.includes(dayOfWeek)) {
                dateInput.classList.add('is-invalid');
                dateError.textContent = 'Doctor is not available on this day';
            } else {
                dateInput.classList.remove('is-invalid');
                dateError.textContent = '';
            }

            // Check if selected time is within available hours
            if (timeInput.value) {
                if (timeInput.value < startTime || timeInput.value > endTime) {
                    timeInput.classList.add('is-invalid');
                    timeError.textContent = `Doctor is only available between ${formatTime(startTime)} and ${formatTime(endTime)}`;
                } else {
                    timeInput.classList.remove('is-invalid');
                    timeError.textContent = '';
                }
            }
        }
    }

    function formatTime(time) {
        return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        });
    }

    // Event listeners
    document.getElementById('doctor_id').addEventListener('change', checkDoctorAvailability);
    document.getElementById('appointment_date').addEventListener('change', checkDoctorAvailability);
    document.getElementById('appointment_time').addEventListener('change', checkDoctorAvailability);

    // Initial check
    checkDoctorAvailability();
});
</script>
<?= $this->endSection() ?> 