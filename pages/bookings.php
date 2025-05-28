<?php
// Set page title
$pageTitle = 'Book Charging Station';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/station-functions.php';
require_once dirname(__DIR__) . '/includes/booking-functions.php';

// Require login
requireLogin();

// Get station ID from query string
$stationId = isset($_GET['station_id']) ? (int)$_GET['station_id'] : null;

// Get station details if station ID is provided
$station = $stationId ? getStationDetails($stationId) : null;

// Get all stations if no specific station is selected
$stations = !$stationId ? getAllStations(true) : null;

// Include header
require_once dirname(__DIR__) . '/includes/header.php';
?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Book a Charging Station</h1>
            <p class="page-subtitle">Select your preferred charging station and time</p>
        </div>

        <div class="booking-container">
            <div class="card">
                <div class="card-body">
                    <form id="booking-form" method="POST" action="process-booking.php" class="needs-validation">
                        <div class="form-group">
                            <label for="station-id" class="form-label">Charging Station</label>
                            <select id="station-id" name="station_id" class="form-control form-select" required>
                                <option value="">Select a station</option>
                                <?php if ($station): ?>
                                    <option value="<?= $station['station_id'] ?>" selected>
                                        <?= htmlspecialchars($station['address_street']) ?>,
                                        <?= htmlspecialchars($station['address_city']) ?>
                                    </option>
                                <?php else: ?>
                                    <?php foreach ($stations as $station): ?>
                                        <option value="<?= $station['station_id'] ?>">
                                            <?= htmlspecialchars($station['address_street']) ?>,
                                            <?= htmlspecialchars($station['address_city']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="charging-point-id" class="form-label">Charging Point</label>
                            <select id="charging-point-id" name="charging_point_id" class="form-control form-select" required disabled>
                                <option value="">Select a charging point</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="booking-date" class="form-label">Date</label>
                            <input type="date" id="booking-date" name="date" class="form-control" required
                                   min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="form-group">
                            <label for="start-time" class="form-label">Start Time</label>
                            <input type="time" id="start-time" name="start_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="end-time" class="form-label">End Time</label>
                            <input type="time" id="end-time" name="end_time" class="form-control" required>
                            <small class="form-text text-muted">Default duration is 1 hour</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-calendar-check"></i> Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="booking-info-card">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Booking Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <i class="fas fa-info-circle"></i>
                            <p>Standard booking duration is 1 hour</p>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <p>Arrive within 10 minutes of your booking time</p>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-bolt"></i>
                            <p>Charging rate: €0.35/kWh</p>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-credit-card"></i>
                            <p>Payment will be processed after charging</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .booking-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--space-6);
            margin-bottom: var(--space-8);
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-bottom: var(--space-3);
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            color: var(--primary);
            font-size: 1.2rem;
            width: 24px;
        }

        .info-item p {
            margin: 0;
            color: var(--gray-700);
        }

        @media (max-width: 992px) {
            .booking-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTimeInput = document.getElementById('start-time');
            const endTimeInput = document.getElementById('end-time');

            // Update end time when start time changes
            startTimeInput.addEventListener('change', function() {
                if (this.value) {
                    // Parse the start time
                    const [hours, minutes] = this.value.split(':');
                    const startDate = new Date();
                    startDate.setHours(parseInt(hours), parseInt(minutes));

                    // Add one hour for default duration
                    const endDate = new Date(startDate.getTime() + (60 * 60 * 1000));

                    // Format end time
                    const endHours = endDate.getHours().toString().padStart(2, '0');
                    const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
                    endTimeInput.value = `${endHours}:${endMinutes}`;
                }
            });
        });
    </script>

    <!-- Include bookings.js for form handling -->
    <script src="<?= APP_URL ?>/assets/js/bookings.js"></script>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>