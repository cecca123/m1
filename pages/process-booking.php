<?php
// Set page title
$pageTitle = 'Processing Booking';

// Include configuration and required functions
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/station-functions.php';
require_once dirname(__DIR__) . '/includes/booking-functions.php';

// Require login
requireLogin();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method.');
    redirect('pages/bookings.php');
}

// Get form data
$stationId = isset($_POST['station_id']) ? (int)$_POST['station_id'] : null;
$chargingPointId = isset($_POST['charging_point_id']) ? (int)$_POST['charging_point_id'] : null;
$date = isset($_POST['date']) ? $_POST['date'] : null;
$startTime = isset($_POST['start_time']) ? $_POST['start_time'] : null;
$endTime = isset($_POST['end_time']) ? $_POST['end_time'] : null;

// Validate required fields
if (!$stationId || !$chargingPointId || !$date || !$startTime || !$endTime) {
    setFlashMessage('error', 'Please fill in all required fields.');
    redirect('pages/bookings.php');
}

// Create booking datetime strings
$bookingStartDatetime = $date . ' ' . $startTime;
$bookingEndDatetime = $date . ' ' . $endTime;

// Create booking
$bookingId = createBooking($_SESSION['user_id'], $chargingPointId, $bookingStartDatetime, $bookingEndDatetime);

if ($bookingId) {
    setFlashMessage('success', 'Booking created successfully!');
    redirect('pages/dashboard.php');
} else {
    setFlashMessage('error', 'Failed to create booking. The selected time slot might be unavailable.');
    redirect('pages/bookings.php?station_id=' . $stationId);
}