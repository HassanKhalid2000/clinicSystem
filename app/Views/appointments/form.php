<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= isset($appointment) ? 'Edit' : 'Schedule' ?> Appointment<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= isset($appointment) ? 'Edit' : 'Schedule New' ?> Appointment</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= isset($appointment) ? site_url('appointments/update/' . $appointment['id']) : site_url('appointments/create') ?>" method="post">
                        <?php if (isset($appointment)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Patient</label>
                                    <select class="form-control select2" id="patient_id" name="patient_id" required <?= isset($_GET['patient_id']) ? 'disabled' : '' ?>>
                                        <option value="">Select Patient</option>
                                        <?php foreach ($patients as $patient): ?>
                                            <option value="<?= $patient['id'] ?>" 
                                                <?= (isset($appointment) && $appointment['patient_id'] == $patient['id']) || 
                                                    (isset($_GET['patient_id']) && $_GET['patient_id'] == $patient['id']) ? 'selected' : '' ?>>
                                                <?= esc($patient['first_name']) ?> <?= esc($patient['last_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($_GET['patient_id'])): ?>
                                        <input type="hidden" name="patient_id" value="<?= $_GET['patient_id'] ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctor_id">Doctor</label>
                                    <select class="form-control select2" id="doctor_id" name="doctor_id" required>
                                        <option value="">Select Doctor</option>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <option value="<?= $doctor['id'] ?>" 
                                                data-days="<?= $doctor['available_days'] ?>"
                                                data-start="<?= $doctor['available_time_start'] ?>"
                                                data-end="<?= $doctor['available_time_end'] ?>"
                                                <?= isset($appointment) && $appointment['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                                                <?= esc($doctor['first_name']) ?> <?= esc($doctor['last_name']) ?> - <?= esc($doctor['specialty']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_date">Date</label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                           value="<?= isset($appointment) ? $appointment['appointment_date'] : old('appointment_date') ?>" required>
                                    <small class="text-muted" id="available_days_info"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointment_time">Time</label>
                                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" 
                                           value="<?= isset($appointment) ? $appointment['appointment_time'] : old('appointment_time') ?>" required>
                                    <small class="text-muted" id="available_time_info"></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reason">Reason for Visit</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required><?= isset($appointment) ? esc($appointment['reason']) : old('reason') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= isset($appointment) ? esc($appointment['notes']) : old('notes') ?></textarea>
                        </div>

                        <?php if (isset($appointment)): ?>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="scheduled" <?= $appointment['status'] == 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                    <option value="completed" <?= $appointment['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $appointment['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Appointment</button>
                            <a href="<?= site_url('appointments') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Update available days and time info when doctor is selected
    $('#doctor_id').change(function() {
        var selected = $(this).find('option:selected');
        var days = selected.data('days');
        var start = selected.data('start');
        var end = selected.data('end');

        if (days) {
            var daysList = days.split(',').join(', ');
            $('#available_days_info').text('Available on: ' + daysList);
        } else {
            $('#available_days_info').text('');
        }

        if (start && end) {
            $('#available_time_info').text('Available between: ' + formatTime(start) + ' - ' + formatTime(end));
        } else {
            $('#available_time_info').text('');
        }
    });

    // Trigger change event to show initial values
    $('#doctor_id').trigger('change');

    // Format time for display
    function formatTime(time) {
        return moment(time, 'HH:mm:ss').format('h:mm A');
    }

    // Validate appointment date and time
    $('form').submit(function(e) {
        var doctor = $('#doctor_id option:selected');
        var date = $('#appointment_date').val();
        var time = $('#appointment_time').val();

        if (!doctor.val()) {
            return true;
        }

        var availableDays = doctor.data('days').split(',');
        var dayOfWeek = moment(date).format('dddd');
        
        if (!availableDays.includes(dayOfWeek)) {
            e.preventDefault();
            alert('Doctor is not available on ' + dayOfWeek + 's');
            return false;
        }

        var startTime = moment(doctor.data('start'), 'HH:mm:ss');
        var endTime = moment(doctor.data('end'), 'HH:mm:ss');
        var appointmentTime = moment(time, 'HH:mm');

        if (appointmentTime.isBefore(startTime) || appointmentTime.isAfter(endTime)) {
            e.preventDefault();
            alert('Please select a time between ' + startTime.format('h:mm A') + ' and ' + endTime.format('h:mm A'));
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 