<?php
// Set page title
$pageTitle = 'Charging Stations';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/station-functions.php';
require_once dirname(__DIR__) . '/includes/booking-functions.php';

// Get available stations
$stations = getAllStations(true);

// Include header
require_once dirname(__DIR__) . '/includes/header.php';
?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Charging Stations</h1>
            <p class="page-subtitle">Find and book available charging stations</p>
        </div>

        <div class="station-filter-container">
            <div class="card">
                <div class="card-body">
                    <form id="station-filter-form" class="form-inline">
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control" placeholder="City or zip code">
                        </div>

                        <div class="form-group">
                            <label for="availability">Availability</label>
                            <select id="availability" name="availability" class="form-control form-select">
                                <option value="">Any Status</option>
                                <option value="available" selected>Available Now</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter Stations
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="station-map-container">
            <div class="card">
                <div class="card-body p-0">
                    <div id="station-map" class="station-map">
                        <div class="rounded-iframe-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d27116.334422518736!2d8.93859901112898!3d44.407442547269085!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sit!2sit!4v1747332788287!5m2!1sit!2sit" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-header">
            <h2>Available Stations</h2>
            <p>Found <?= count($stations) ?> charging stations</p>
        </div>

        <div class="station-grid">
            <?php foreach ($stations as $station): ?>
                <div class="station-card">
                    <div class="station-header">
                        <h3 class="station-title"><?= htmlspecialchars($station['address_street']) ?></h3>
                        <p class="station-address">
                            <?= htmlspecialchars($station['address_city']) ?>,
                            <?= htmlspecialchars($station['address_municipality']) ?>
                            <?= htmlspecialchars($station['address_zipcode']) ?>
                        </p>
                    </div>

                    <div class="station-body">
                        <?php
                        // Get charging points for this station
                        $chargingPoints = getStationChargingPoints($station['station_id']);
                        $availablePoints = array_filter($chargingPoints, function($point) {
                            return $point['status'] === 'available';
                        });
                        $availableCount = count($availablePoints);
                        $totalCount = count($chargingPoints);
                        $availabilityPercentage = $totalCount > 0 ? ($availableCount / $totalCount) * 100 : 0;
                        ?>

                        <div class="station-availability">
                            <span><?= $availableCount ?> of <?= $totalCount ?> available</span>
                            <div class="availability-bar">
                                <div class="availability-progress"
                                     style="width: <?= $availabilityPercentage ?>%;"
                                     data-percentage="<?= round($availabilityPercentage) ?>">
                                </div>
                            </div>
                            <span><?= round($availabilityPercentage) ?>%</span>
                        </div>

                        <div class="charging-points-list">
                            <h4>Charging Points</h4>
                            <div class="points-grid">
                                <?php foreach ($chargingPoints as $point): ?>
                                    <div class="point-item <?= $point['status'] ?>">
                                        <div class="point-status">
                                            <span class="status-dot"></span>
                                            <?= ucfirst($point['status']) ?>
                                        </div>
                                        <div class="point-info">
                                            Point #<?= $point['charging_point_id'] ?>
                                            (<?= $point['slots_num'] ?> slots)
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="station-footer">
                        <div class="station-actions">
                            <?php if ($availableCount > 0 && isLoggedIn()): ?>
                                <a href="<?= APP_URL ?>/pages/bookings.php?station_id=<?= $station['station_id'] ?>"
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-calendar-check"></i> Book Now
                                </a>
                            <?php elseif ($availableCount > 0): ?>
                                <a href="<?= APP_URL ?>/pages/login.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sign-in-alt"></i> Login to Book
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-times-circle"></i> No Available Points
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        .page-header {
            margin-bottom: var(--space-6);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: var(--space-2);
        }

        .page-subtitle {
            font-size: 1.1rem;
            color: var(--gray-600);
        }

        .station-filter-container {
            margin-bottom: var(--space-6);
        }

        .station-map-container {
            margin-bottom: var(--space-6);
        }

        .station-map {
            height: 400px;
            background-color: var(--gray-100);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .section-header {
            margin-bottom: var(--space-6);
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--space-1);
        }

        .section-header p {
            color: var(--gray-600);
        }

        .rounded-iframe-container {
            display: block;
            width: 100%;
            max-width: 100%;
            border-radius: 20px;
            overflow: hidden;
            margin: 0;
            height: 400px;
        }

        .rounded-iframe-container iframe {
            width: 100%;
            height: 100%;
            display: block;
            border: none;
        }

        #station-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-4);
        }

        #station-filter-form .form-group {
            flex: 1;
            min-width: 200px;
        }

        #station-filter-form button {
            margin-top: 1.85rem;
        }

        .charging-points-list {
            margin-top: var(--space-4);
        }

        .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: var(--space-3);
            margin-top: var(--space-3);
        }

        .point-item {
            background-color: var(--gray-100);
            border-radius: var(--radius-md);
            padding: var(--space-3);
        }

        .point-item.available {
            background-color: var(--success);
            color: var(--white);
        }

        .point-item.not-available {
            background-color: var(--error);
            color: var(--white);
        }

        .point-status {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            margin-bottom: var(--space-2);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
        }

        .point-info {
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            #station-filter-form button {
                margin-top: var(--space-3);
                width: 100%;
            }

            .points-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
        }
    </style>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>