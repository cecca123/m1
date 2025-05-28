<?php

// Set page title
$pageTitle = 'Manage Users';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth-functions.php';

// Require admin access
requireAdmin();

// Get database connection (PDO)
$conn = getDbConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request.');
        redirect('admin/users.php');
    }

    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);

    try {
        switch ($action) {
            case 'toggle_status':
                $active = (int)($_POST['active'] ?? 0);
                $sql = "UPDATE Users SET active = :active WHERE user_id = :user_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':active' => $active, ':user_id' => $userId]);
                setFlashMessage('success', 'User status updated successfully.');
                break;

            case 'delete_user':
                $sql = "DELETE FROM Users WHERE user_id = :user_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':user_id' => $userId]);
                setFlashMessage('success', 'User deleted successfully.');
                break;
        }
    } catch (PDOException $e) {
        setFlashMessage('error', 'Database error: ' . $e->getMessage());
    }
    redirect('admin/users.php');
}

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

try {
    // Get total number of users
    $stmt = $conn->query("SELECT COUNT(*) as count FROM Users");
    $totalUsers = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $totalPages = ceil($totalUsers / $perPage);

    // Get users with bookings and charges count
    $sql = "
        SELECT 
            u.*, 
            (SELECT COUNT(*) FROM Bookings b WHERE b.user_id = u.user_id) as total_bookings,
            (SELECT COUNT(*) FROM Chargings c WHERE c.user_id = u.user_id) as total_charges
        FROM Users u
        ORDER BY u.user_id DESC
        LIMIT :offset, :perPage
    ";
    $stmt = $conn->prepare($sql);
    // Bind as integers explicitly
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    setFlashMessage('error', 'Failed to fetch users: ' . $e->getMessage());
}

// Generate CSRF token
$csrfToken = generateCsrfToken();

// Include header and navbar (HTML output starts here)
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/admin-navbar.php';
?>

<div class="container">
    <div class="admin-container">

        <!-- Main Content -->
        <div class="admin-content">
            <div class="page-header">
                <h1>Manage Users</h1>
            </div>

            <!-- Users List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Bookings</th>
                                <th>Charges</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['total_bookings']) ?></td>
                                    <td><?= htmlspecialchars($user['total_charges']) ?></td>
                                    <td>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No users found.</td>
                                </tr>
                            <?php endif; ?>
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

<!-- View User Modal -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle view user button clicks (if implemented in future)
        document.querySelectorAll('.view-user').forEach(button => {
            button.addEventListener('click', function() {
                const user = JSON.parse(this.dataset.user);
                document.getElementById('view_name').textContent = user.name;
                document.getElementById('view_email').textContent = user.email;
                document.getElementById('view_bookings').textContent = user.total_bookings;
                document.getElementById('view_charges').textContent = user.total_charges;
                document.getElementById('view_status').textContent = user.active ? 'Active' : 'Inactive';
            });
        });
    });
</script>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>
