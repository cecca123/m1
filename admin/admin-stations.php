<?php
// Set page title
$pageTitle = 'Manage Stations';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/admin-navbar.php';

// Require admin access
requireAdmin();

// Get database connection
$conn = getDbConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid request.');
        redirect('admin/stations.php');
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_station':
            // Add new station
            $street = sanitizeInput($_POST['address_street']);
            $city = sanitizeInput($_POST['address_city']);
            $municipality = sanitizeInput($_POST['address_municipality']);
            $civic = sanitizeInput($_POST['address_civic_num']);
            $zipcode = sanitizeInput($_POST['address_zipcode']);
            $columns = (int)$_POST['columns_num'];
            $lat = (float)$_POST['latitude'];
            $lng = (float)$_POST['longitude'];

            try {
                // Start transaction
                $conn->beginTransaction();

                // Insert station
                $sql = "INSERT INTO Stations (address_street, address_city, address_municipality, address_civic_num, address_zipcode, columns_num, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$street, $city, $municipality, $civic, $zipcode, $columns, $lat, $lng]);
                
                $stationId = $conn->lastInsertId();

                // Add charging points
                for ($i = 0; $i < $columns * 2; $i++) {
                    $sql = "INSERT INTO Charging_Points (charging_point_state, slots_num, station_id) VALUES ('available', 2, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$stationId]);
                }

                $conn->commit();
                setFlashMessage('success', 'Station added successfully.');
            } catch (Exception $e) {
                $conn->rollBack();
                setFlashMessage('error', 'Error adding station: ' . $e->getMessage());
            }
            break;

        case 'edit_station':
            $stationId = (int)$_POST['station_id'];
            $street = sanitizeInput($_POST['address_street']);
            $city = sanitizeInput($_POST['address_city']);
            $municipality = sanitizeInput($_POST['address_municipality']);
            $civic = sanitizeInput($_POST['address_civic_num']);
            $zipcode = sanitizeInput($_POST['address_zipcode']);
            $lat = (float)$_POST['latitude'];
            $lng = (float)$_POST['longitude'];

            try {
                $sql = "UPDATE Stations SET 
                    address_street = ?, 
                    address_city = ?, 
                    address_municipality = ?, 
                    address_civic_num = ?, 
                    address_zipcode = ?, 
                    latitude = ?, 
                    longitude = ? 
                    WHERE station_id = ?";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $street,
                    $city,
                    $municipality,
                    $civic,
                    $zipcode,
                    $lat,
                    $lng,
                    $stationId
                ]);

                setFlashMessage('success', 'Station updated successfully.');
            } catch (Exception $e) {
                setFlashMessage('error', 'Error updating station: ' . $e->getMessage());
            }
            break;

        case 'delete_station':
            $stationId = (int)$_POST['station_id'];

            try {
                // Start transaction
                $conn->beginTransaction();

                // First delete related charging points
                $sql = "DELETE FROM Charging_Points WHERE station_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$stationId]);

                // Then delete the station
                $sql = "DELETE FROM Stations WHERE station_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$stationId]);

                $conn->commit();
                setFlashMessage('success', 'Station deleted successfully.');
            } catch (Exception $e) {
                $conn->rollBack();
                setFlashMessage('error', 'Error deleting station: ' . $e->getMessage());
            }
            break;
    }

    redirect('admin/stations.php');
}

// Get all stations
$stmt = $conn->prepare("SELECT * FROM Stations ORDER BY station_id DESC");
$stmt->execute();
$stations = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <h1>Manage Stations</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStationModal">
                    <i class="fas fa-plus"></i> Add New Station
                </button>
            </div>

            <!-- Stations List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>Columns</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stations as $station): ?>
                                <tr>
                                    <td><?= $station['station_id'] ?></td>
                                    <td><?= htmlspecialchars($station['address_street']) ?></td>
                                    <td><?= htmlspecialchars($station['address_city']) ?></td>
                                    <td><?= $station['columns_num'] ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-station" 
                                                data-station='<?= json_encode($station) ?>'
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editStationModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this station?');">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="action" value="delete_station">
                                            <input type="hidden" name="station_id" value="<?= $station['station_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

<!-- Add Station Modal -->
<div class="modal fade" id="addStationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Station</h5>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="add_station">

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Street Address</label>
                        <input type="text" name="address_street" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>City</label>
                        <input type="text" name="address_city" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Municipality</label>
                        <input type="text" name="address_municipality" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Civic Number</label>
                        <input type="text" name="address_civic_num" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>ZIP Code</label>
                        <input type="text" name="address_zipcode" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Number of Columns</label>
                        <input type="number" name="columns_num" class="form-control" min="1" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Latitude</label>
                        <input type="number" name="latitude" class="form-control" step="0.000001" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Longitude</label>
                        <input type="number" name="longitude" class="form-control" step="0.000001" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Station</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Station Modal -->
<div class="modal fade" id="editStationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Station</h5>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="action" value="edit_station">
                <input type="hidden" name="station_id" id="edit_station_id">

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Street Address</label>
                        <input type="text" name="address_street" id="edit_address_street" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>City</label>
                        <input type="text" name="address_city" id="edit_address_city" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Municipality</label>
                        <input type="text" name="address_municipality" id="edit_address_municipality" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Civic Number</label>
                        <input type="text" name="address_civic_num" id="edit_address_civic_num" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>ZIP Code</label>
                        <input type="text" name="address_zipcode" id="edit_address_zipcode" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Latitude</label>
                        <input type="number" name="latitude" id="edit_latitude" class="form-control" step="0.000001" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Longitude</label>
                        <input type="number" name="longitude" id="edit_longitude" class="form-control" step="0.000001" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit station button clicks
    document.querySelectorAll('.edit-station').forEach(button => {
        button.addEventListener('click', function() {
            const station = JSON.parse(this.dataset.station);

            // Fill the edit form with station data
            document.getElementById('edit_station_id').value = station.station_id;
            document.getElementById('edit_address_street').value = station.address_street;
            document.getElementById('edit_address_city').value = station.address_city;
            document.getElementById('edit_address_municipality').value = station.address_municipality;
            document.getElementById('edit_address_civic_num').value = station.address_civic_num;
            document.getElementById('edit_address_zipcode').value = station.address_zipcode;
            document.getElementById('edit_latitude').value = station.latitude;
            document.getElementById('edit_longitude').value = station.longitude;
        });
    });
});
</script>

<?php
// Include footer
require_once dirname(__DIR__) . '/includes/footer.php';
?>