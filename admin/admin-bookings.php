<?php

// Set page title
$pageTitle = 'Manage Bookings';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/admin-navbar.php';

// Require admin access
requireAdmin();

// Get database connection (PDO)
$conn = getDbConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request.');
        redirect('admin/bookings.php');
    }

    $action = $_POST['action'] ?? '';
    $bookingId = (int)($_POST['booking_id'] ?? 0);

    switch ($action) {
        case 'cancel_booking':
            $sql = "DELETE FROM Bookings WHERE booking_id = :booking_id";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                setFlashMessage('error', 'Database error.');
                break;
            }
            if ($stmt->execute([':booking_id' => $bookingId])) {
                setFlashMessage('success', 'Booking cancelled successfully.');
            } else {
                setFlashMessage('error', 'Error cancelling booking.');
            }
            break;
    }

    redirect('admin-bookings.php');
}

// Get bookings list with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Total bookings count
$stmt = $conn->query("SELECT COUNT(*) as count FROM Bookings");
$totalBookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
$totalPages = ceil($totalBookings / $perPage);

// Bookings query with pagination - LIMIT values concatenated safely after casting to int
$sql = "
    SELECT b.*, 
           u.name AS user_name,
           s.address_street,
           s.address_city
    FROM Bookings b
    JOIN Users u ON b.user_id = u.user_id
    JOIN Charging_Points cp ON b.charging_point_id = cp.charging_point_id
    JOIN Stations s ON cp.station_id = s.station_id
    ORDER BY b.booking_datetime DESC
    LIMIT $offset, $perPage
";

$stmt = $conn->prepare($sql);
if (!$stmt->execute()) {
    die('Failed to fetch bookings.');
}

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate CSRF token
$csrfToken = generateCsrfToken();

// Include header
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container">
    <div class="admin-container">

        <!-- Main Content -->
        <div class="admin-content">
            <div class="page-header">
                <h1>Manage Bookings</h1>
            </div>

            <!-- Bookings List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Station</th>
                                <th>Date & Time</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?= htmlspecialchars($booking['booking_id']) ?></td>
                                    <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($booking['address_street']) ?>,
                                        <?= htmlspecialchars($booking['address_city']) ?>
                                    </td>
                                    <td><?= date('M j, Y g:i A', strtotime($booking['booking_datetime'])) ?></td>
                                    <td>
                                        <?php if (!isset($booking['booking_status']) || $booking['booking_status'] !== 'cancelled'): ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                <input type="hidden" name="action" value="cancel_booking">
                                                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancel Booking">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination-container">
                            <nav>
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle view booking button clicks
        document.querySelectorAll('.view-booking').forEach(button => {
            button.addEventListener('click', function() {
                const booking = JSON.parse(this.dataset.booking);

                // Fill the modal with booking data
                document.getElementById('view_booking_id').textContent = booking.booking_id;
                document.getElementById('view_user_name').textContent = booking.user_name;
                document.getElementById('view_station').textContent = `${booking.address_street}, ${booking.address_city}`;
                document.getElementById('view_datetime').textContent = new Date(booking.booking_datetime).toLocaleString();
                document.getElementById('view_status').textContent = booking.booking_status.charAt(0).toUpperCase() + booking.booking_status.slice(1);
            });
        });
    });
</script>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>