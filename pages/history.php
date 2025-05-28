<?php
// Set page title
$pageTitle = 'Charging History';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth-functions.php';

// Require login
requireLogin();

// Get charging history
$userId = $_SESSION['user_id'];
$conn = getDbConnection();

$query = "SELECT 
    ch.charging_id,
    ch.start_datetime,
    ch.end_datetime,
    ch.energy_consumed,
    ch.cost,
    s.address_street,
    s.address_city
FROM Chargings ch
JOIN Charging_Points cp ON ch.charging_point_id = cp.charging_point_id
JOIN Stations s ON cp.station_id = s.station_id
WHERE ch.user_id = ?
ORDER BY ch.start_datetime DESC";

$stmt = $conn->prepare($query);
$stmt->execute([$userId]);
$chargingHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Charging History</h1>
        <p class="page-subtitle">View your past charging sessions</p>
    </div>

    <div class="history-container">
        <?php if (empty($chargingHistory)): ?>
            <div class="card">
                <div class="card-body text-center">
                    <div class="empty-state">
                        <i class="fas fa-history fa-3x"></i>
                        <h3>No Charging History</h3>
                        <p>You haven't completed any charging sessions yet.</p>
                        <a href="<?= APP_URL ?>/pages/stations.php" class="btn btn-primary">
                            <i class="fas fa-plug"></i> Find Charging Stations
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Location</th>
                                    <th>Duration</th>
                                    <th>Energy</th>
                                    <th>Cost</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chargingHistory as $session): ?>
                                    <tr>
                                        <td>
                                            <div class="session-datetime">
                                                <div class="session-date">
                                                    <?= date('M j, Y', strtotime($session['start_time'])) ?>
                                                </div>
                                                <div class="session-time">
                                                    <?= date('H:i', strtotime($session['start_time'])) ?> - 
                                                    <?= date('H:i', strtotime($session['end_time'])) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session-location">
                                                <div class="location-street">
                                                    <?= htmlspecialchars($session['address_street']) ?>
                                                </div>
                                                <div class="location-city">
                                                    <?= htmlspecialchars($session['address_city']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $duration = strtotime($session['end_time']) - strtotime($session['start_time']);
                                            $hours = floor($duration / 3600);
                                            $minutes = floor(($duration % 3600) / 60);
                                            echo $hours . 'h ' . $minutes . 'm';
                                            ?>
                                        </td>
                                        <td>
                                            <div class="energy-consumed">
                                                <?= number_format($session['energy_consumed'], 2) ?> kWh
                                            </div>
                                        </td>
                                        <td>
                                            <div class="session-cost">
                                                €<?= number_format($session['total_cost'], 2) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="payment-info">
                                                <i class="fas fa-credit-card"></i>
                                                ****<?= htmlspecialchars($session['payment_card_last4']) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .history-container {
        margin-bottom: var(--space-8);
    }

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

    .empty-state {
        padding: var(--space-8) var(--space-4);
        text-align: center;
    }

    .empty-state i {
        color: var(--gray-400);
        margin-bottom: var(--space-4);
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--gray-700);
        margin-bottom: var(--space-2);
    }

    .empty-state p {
        color: var(--gray-600);
        margin-bottom: var(--space-4);
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background-color: var(--gray-100);
        font-weight: 600;
        color: var(--gray-700);
    }

    .session-datetime {
        line-height: 1.3;
    }

    .session-date {
        font-weight: 600;
        color: var(--gray-800);
    }

    .session-time {
        color: var(--gray-600);
        font-size: 0.9rem;
    }

    .session-location {
        line-height: 1.3;
    }

    .location-street {
        font-weight: 500;
        color: var(--gray-800);
    }

    .location-city {
        color: var(--gray-600);
        font-size: 0.9rem;
    }

    .energy-consumed {
        color: var(--primary);
        font-weight: 500;
    }

    .session-cost {
        font-weight: 600;
        color: var(--gray-800);
    }

    .payment-info {
        color: var(--gray-600);
    }

    .payment-info i {
        margin-right: var(--space-2);
    }

    @media (max-width: 768px) {
        .table-responsive {
            margin: calc(var(--space-4) * -1);
            width: calc(100% + var(--space-8));
        }
    }
</style>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>