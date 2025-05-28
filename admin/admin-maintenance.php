<?php
// Set page title
$pageTitle = 'Maintenance';

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
        redirect('admin/maintenance.php');
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_malfunction':
            $description = sanitizeInput($_POST['description']);
            $stationId = (int)$_POST['station_id'];
            $state = 'reported';

            // Create malfunction record
            $sql = "INSERT INTO Malfunctions (description, state) VALUES (:description, :state)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':state', $state, PDO::PARAM_STR);

            if ($stmt->execute()) {
                setFlashMessage('success', 'Malfunction reported successfully.');
            } else {
                setFlashMessage('error', 'Error reporting malfunction.');
            }
            break;

        case 'update_malfunction':
            $malfunctionId = (int)$_POST['malfunction_id'];
            $description = sanitizeInput($_POST['description']);
            $state = sanitizeInput($_POST['state']);

            $sql = "UPDATE Malfunctions SET description = :description, state = :state WHERE malfunction_id = :malfunction_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':state', $state, PDO::PARAM_STR);
            $stmt->bindValue(':malfunction_id', $malfunctionId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                setFlashMessage('success', 'Malfunction updated successfully.');
            } else {
                setFlashMessage('error', 'Error updating malfunction.');
            }
            break;

        case 'delete_malfunction':
            $malfunctionId = (int)$_POST['malfunction_id'];

            $sql = "DELETE FROM Malfunctions WHERE malfunction_id = :malfunction_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':malfunction_id', $malfunctionId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                setFlashMessage('success', 'Malfunction deleted successfully.');
            } else {
                setFlashMessage('error', 'Error deleting malfunction.');
            }
            break;
    }

    redirect('admin/maintenance.php');
}

// Get stations list
$stationsStmt = $conn->query("SELECT * FROM Stations ORDER BY station_id");
$stations = $stationsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get malfunctions list
$malfunctionsQuery = "
    SELECT m.*, s.address_street, s.address_city
FROM Malfunctions m
JOIN Reports r ON m.report_id = r.report_id
JOIN Admins a ON a.admin_id = r.admin_id
JOIN Users u ON a.user_id = u.user_id
JOIN Chargings c ON u.user_id = c.user_id
JOIN Charging_Points cp ON c.charging_point_id = cp.charging_point_id
JOIN Stations s ON cp.station_id = s.station_id
ORDER BY m.malfunction_id DESC;
";

$malfunctions = $conn->query($malfunctionsQuery)->fetchAll(PDO::FETCH_ASSOC);

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
                <h1>Maintenance</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMalfunctionModal">
                    <i class="fas fa-plus"></i> Report Malfunction
                </button>
            </div>

            <!-- Malfunctions List -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Reported Malfunctions</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Station</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($malfunctions as $malfunction): ?>
                                <tr>
                                    <td><?= htmlspecialchars($malfunction['malfunction_id']) ?></td>
                                    <td>
                                        <?php if ($malfunction['address_street'] && $malfunction['address_city']): ?>
                                            <?= htmlspecialchars($malfunction['address_street']) ?>,
                                            <?= htmlspecialchars($malfunction['address_city']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($malfunction['description']) ?></td>
                                    <td>
                                        <span class="badge bg-<?=
                                        $malfunction['state'] === 'resolved' ? 'success' :
                                            ($malfunction['state'] === 'in_progress' ? 'warning' : 'danger')
                                        ?>">
                                            <?= ucfirst(str_replace('_', ' ', $malfunction['state'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary update-malfunction"
                                                    data-malfunction='<?= json_encode($malfunction, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateMalfunctionModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this malfunction?');">
                                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                <input type="hidden" name="action" value="delete_malfunction">
                                                <input type="hidden" name="malfunction_id" value="<?= $malfunction['malfunction_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Malfunction Modal -->
<div class="modal fade" id="addMalfunctionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Malfunction</h5>
                
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="add_malfunction">

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Station</label>
                        <select name="station_id" class="form-control" required>
                            <option value="">Select a station</option>
                            <?php foreach ($stations as $station): ?>
                                <option value="<?= htmlspecialchars($station['station_id']) ?>">
                                    <?= htmlspecialchars($station['address_street']) ?>,
                                    <?= htmlspecialchars($station['address_city']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Report Malfunction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Malfunction Modal -->
<div class="modal fade" id="updateMalfunctionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Malfunction</h5>
                
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="update_malfunction">
                <input type="hidden" name="malfunction_id" id="update_malfunction_id">

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Description</label>
                        <textarea name="description" id="update_description" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select name="state" class="form-control" required>
                            <option value="reported">Reported</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>

                    <div class="malfunction-details">
                        <p><strong>Station:</strong> <span id="update_station"></span></p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Malfunction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-group {
        display: flex;
        gap: 0.25rem;
    }
    
    .badge {
        font-size: 0.875rem;
        padding: 0.5em 0.75em;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle update malfunction button clicks
        document.querySelectorAll('.update-malfunction').forEach(button => {
            button.addEventListener('click', function() {
                const malfunction = JSON.parse(this.dataset.malfunction);

                // Fill the modal with malfunction data
                document.getElementById('update_malfunction_id').value = malfunction.malfunction_id;
                document.getElementById('update_description').value = malfunction.description;
                document.getElementById('update_station').textContent = 
                    malfunction.address_street && malfunction.address_city ? 
                    `${malfunction.address_street}, ${malfunction.address_city}` : 
                    'Not assigned';

                // Set current status
                const stateSelect = document.querySelector('#updateMalfunctionModal select[name="state"]');
                stateSelect.value = malfunction.state;
            });
        });
    });
</script>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>