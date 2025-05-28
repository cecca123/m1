<?php
// Set page title
$pageTitle = 'Admin Dashboard';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/admin-navbar.php';

// Require admin access
requireAdmin();

// Get database connection
$conn = getDbConnection();

// Get statistics
$stats = [
    'users' => $conn->query("SELECT COUNT(*) as count FROM Users")->fetch(PDO::FETCH_ASSOC)['count'],
    'stations' => $conn->query("SELECT COUNT(*) as count FROM Stations")->fetch(PDO::FETCH_ASSOC)['count'],
    'bookings' => $conn->query("SELECT COUNT(*) as count FROM Bookings")->fetch(PDO::FETCH_ASSOC)['count'],
    'active_bookings' => $conn->query("SELECT COUNT(*) as count FROM Bookings WHERE booking_datetime >= NOW()")->fetch(PDO::FETCH_ASSOC)['count']
];

// Get recent activity
$stmt = $conn->query("
    SELECT 'booking' as type, b.booking_datetime as datetime, u.name as user_name, 
           s.address_street as location
    FROM Bookings b
    JOIN Users u ON b.user_id = u.user_id
    JOIN Charging_Points cp ON b.charging_point_id = cp.charging_point_id
    JOIN Stations s ON cp.station_id = s.station_id
    ORDER BY b.booking_datetime DESC
    LIMIT 10
");
$recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container">
    <div class="admin-container">

        <!-- Main Content -->
        <div class="admin-content">
            <div class="page-header">
                <h1>Admin Dashboard</h1>
                <p>Overview of your charging station network</p>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-title">Total Users</div>
                    <div class="stat-card-value"><?= $stats['users'] ?></div>
                    <div class="stat-card-info">Registered users</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-title">Charging Stations</div>
                    <div class="stat-card-value"><?= $stats['stations'] ?></div>
                    <div class="stat-card-info">Active stations</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-title">Total Bookings</div>
                    <div class="stat-card-value"><?= $stats['bookings'] ?></div>
                    <div class="stat-card-info">All time</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-title">Active Bookings</div>
                    <div class="stat-card-value"><?= $stats['active_bookings'] ?></div>
                    <div class="stat-card-info">Current and upcoming</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="card mb-6">
                <div class="card-header">
                    <h2 class="card-title">Usage Analytics</h2>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Bookings Over Time</h3>
                            <div class="chart-filters">
                                <button class="chart-filter" data-period="weekly">Weekly</button>
                                <button class="chart-filter active" data-period="monthly">Monthly</button>
                                <button class="chart-filter" data-period="yearly">Yearly</button>
                            </div>
                        </div>
                        <div class="chart-body" id="bookings-chart"></div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Activity</h2>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <?php foreach ($recentActivity as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        New Booking by <?= htmlspecialchars($activity['user_name']) ?>
                                    </div>
                                    <div class="activity-details">
                                        <?= htmlspecialchars($activity['location']) ?> -
                                        <?= date('M j, Y g:i A', strtotime($activity['datetime'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>
