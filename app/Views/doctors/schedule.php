<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Doctor Schedule<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
.fc-event {
    cursor: pointer;
}
.fc-event-title {
    white-space: normal;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Doctor Schedule</h1>
        <div>
            <a href="<?= base_url('appointments/create?doctor_id=' . $doctor['id']) ?>" class="btn btn-primary">
                <i class="fas fa-calendar-plus"></i> New Appointment
            </a>
            <a href="<?= base_url('doctors/view/' . $doctor['id']) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Details
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Doctor Information</h5>
                </div>
                <div class="card-body">
                    <h6><?= esc($doctor['first_name']) . ' ' . esc($doctor['last_name']) ?></h6>
                    <p class="text-muted mb-2"><?= esc($doctor['specialization']) ?></p>
                    
                    <hr>
                    
                    <h6 class="mb-3">Available Days</h6>
                    <?php 
                    $availableDays = json_decode($doctor['available_days']);
                    foreach ($availableDays as $day): 
                    ?>
                    <span class="badge bg-primary me-1 mb-1"><?= $day ?></span>
                    <?php endforeach; ?>

                    <hr>

                    <h6 class="mb-2">Working Hours</h6>
                    <p class="mb-0">
                        <?= date('h:i A', strtotime($doctor['available_time_start'])) ?> - 
                        <?= date('h:i A', strtotime($doctor['available_time_end'])) ?>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Legend</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2" style="width: 20px; height: 20px; background-color: #3788d8; border-radius: 4px;"></div>
                        <span>Scheduled</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2" style="width: 20px; height: 20px; background-color: #28a745; border-radius: 4px;"></div>
                        <span>Completed</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-2" style="width: 20px; height: 20px; background-color: #dc3545; border-radius: 4px;"></div>
                        <span>Cancelled</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Patient</dt>
                    <dd class="col-sm-8" id="modalPatient"></dd>

                    <dt class="col-sm-4">Date & Time</dt>
                    <dd class="col-sm-8" id="modalDateTime"></dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8" id="modalStatus"></dd>

                    <dt class="col-sm-4">Reason</dt>
                    <dd class="col-sm-8" id="modalReason"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <a href="#" id="viewAppointmentBtn" class="btn btn-primary">View Details</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '<?= $doctor['available_time_start'] ?>',
        slotMaxTime: '<?= $doctor['available_time_end'] ?>',
        businessHours: {
            daysOfWeek: <?= json_encode(array_map(function($day) {
                return ['Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 
                        'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6][$day];
            }, json_decode($doctor['available_days']))) ?>,
            startTime: '<?= $doctor['available_time_start'] ?>',
            endTime: '<?= $doctor['available_time_end'] ?>'
        },
        events: '<?= base_url('appointments/calendar-data?doctor_id=' . $doctor['id']) ?>',
        eventClick: function(info) {
            const event = info.event;
            const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            
            document.getElementById('modalPatient').textContent = event.title;
            document.getElementById('modalDateTime').textContent = moment(event.start).format('MMMM D, YYYY h:mm A');
            document.getElementById('modalStatus').innerHTML = `
                <span class="badge bg-${event.backgroundColor === '#3788d8' ? 'primary' : 
                                      (event.backgroundColor === '#28a745' ? 'success' : 'danger')}">
                    ${event.extendedProps.status}
                </span>
            `;
            document.getElementById('modalReason').textContent = event.extendedProps.reason;
            document.getElementById('viewAppointmentBtn').href = `<?= base_url('appointments/view/') ?>/${event.id}`;
            
            modal.show();
        }
    });
    calendar.render();
});
</script>
<?= $this->endSection() ?> 