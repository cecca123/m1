<?php
/**
 * Booking related functions
 */

/**
 * Create a new booking
 *
 * @param int $userId User ID
 * @param int $chargingPointId Charging point ID
 * @param string $bookingStartDatetime Booking start datetime
 * @param string $bookingEndDatetime Booking end datetime
 * @return int|bool Booking ID on success, false on failure
 */
function createBooking($userId, $chargingPointId, $bookingStartDatetime, $bookingEndDatetime) {
    error_log("🔧 createBooking() called with userId=$userId, chargingPointId=$chargingPointId, start=$bookingStartDatetime, end=$bookingEndDatetime");

    // Check availability
    if (!isChargingPointAvailable($chargingPointId, $bookingStartDatetime, $bookingEndDatetime)) {
        error_log("❌ Charging point $chargingPointId is not available for the requested time slot");
        return false;
    }

    // Prepare data for insert
    $bookingData = [
        'user_id' => $userId,
        'charging_point_id' => $chargingPointId,
        'booking_datetime' => $bookingStartDatetime,
        'booking_end_datetime' => $bookingEndDatetime
    ];

    error_log("📦 Trying to insert booking: " . json_encode($bookingData));

    // Insert booking
    $bookingId = insert('Bookings', $bookingData);
    error_log("🔍 isChargingPointAvailable() called with CP=$chargingPointId, start=$bookingStartDatetime, end=$bookingEndDatetime");

    if ($bookingId) {
        error_log("✅ Booking inserted successfully with ID $bookingId");
        return $bookingId;
    } else {
        error_log("❌ Booking insert failed");
        return false;
    }
}

/**
 * Check if a charging point is available for booking
 *
 * @param int $chargingPointId Charging point ID
 * @param string $startDatetime Start datetime
 * @param string $endDatetime End datetime
 * @return bool True if available, false if not
 */
function isChargingPointAvailable($chargingPointId, $startDatetime, $endDatetime) {
    // Check for existing bookings that overlap with the requested time slot
    $sql = "SELECT COUNT(*) as count FROM Bookings 
            WHERE charging_point_id = ? 
            AND (
                (booking_datetime <= ? AND booking_end_datetime > ?) OR
                (booking_datetime < ? AND booking_end_datetime >= ?) OR
                (booking_datetime >= ? AND booking_end_datetime <= ?)
            )";

    $result = fetchOne($sql, [
        $chargingPointId,
        $startDatetime,
        $startDatetime,
        $endDatetime,
        $endDatetime,
        $startDatetime,
        $endDatetime
    ]);

    return $result['count'] == 0;
}

/**
 * Cancel a booking
 *
 * @param int $bookingId Booking ID
 * @param int $userId User ID (for verification)
 * @return bool True on success, false on failure
 */
function cancelBooking($bookingId, $userId) {
    // Verify that the booking belongs to the user
    $booking = fetchOne(
        "SELECT * FROM Bookings WHERE booking_id = ? AND user_id = ?",
        [$bookingId, $userId]
    );

    if (!$booking) {
        return false;
    }

    // Delete the booking
    return delete('Bookings', 'booking_id = ?', [$bookingId]);
}

/**
 * Get booking details
 *
 * @param int $bookingId Booking ID
 * @return array|null Booking details or null if not found
 */
function getBookingDetails($bookingId) {
    $sql = "SELECT b.*, cp.slots_num,
                  s.address_street, s.address_city, s.address_municipality,
                  u.name as user_name, u.email as user_email
            FROM Bookings b
            JOIN Charging_Points cp ON b.charging_point_id = cp.charging_point_id
            JOIN Stations s ON cp.station_id = s.station_id
            JOIN Users u ON b.user_id = u.user_id
            WHERE b.booking_id = ?";

    return fetchOne($sql, [$bookingId]);
}

/**
 * Get current and upcoming bookings for a user
 *
 * @param int $userId User ID
 * @return array Array of current and upcoming bookings
 */
function getUserUpcomingBookings($userId) {
    $currentDatetime = date('Y-m-d H:i:s');

    $sql = "SELECT b.*, cp.slots_num,
                  s.address_street, s.address_city
            FROM Bookings b
            JOIN Charging_Points cp ON b.charging_point_id = cp.charging_point_id
            JOIN Stations s ON cp.station_id = s.station_id
            WHERE b.user_id = ? 
            AND b.booking_datetime >= ?
            ORDER BY b.booking_datetime";

    return fetchAll($sql, [$userId, $currentDatetime]);
}