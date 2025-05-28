<?php
/**
 * General utility functions
 *
 * This file contains utility functions used throughout the application.
 */

/**
 * Format a date and time
 *
 * @param string $dateTime Date and time string
 * @param string $format Output format (default: 'Y-m-d H:i:s')
 * @return string Formatted date and time
 */
function formatDateTime($dateTime, $format = 'Y-m-d H:i:s') {
    $dt = new DateTime($dateTime);
    return $dt->format($format);
}

/**
 * Format a date
 *
 * @param string $date Date string
 * @param string $format Output format (default: 'Y-m-d')
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d') {
    $dt = new DateTime($date);
    return $dt->format($format);
}

/**
 * Format a time
 *
 * @param string $time Time string
 * @param string $format Output format (default: 'H:i')
 * @return string Formatted time
 */
function formatTime($time, $format = 'H:i') {
    $dt = new DateTime($time);
    return $dt->format($format);
}

/**
 * Format currency amount
 *
 * @param float $amount Amount to format
 * @param string $currency Currency symbol (default: '€')
 * @return string Formatted currency amount
 */
function formatCurrency($amount, $currency = '€') {
    return $currency . number_format($amount, 2);
}

/**
 * Format energy amount in kWh
 *
 * @param float $amount Energy amount in kWh
 * @return string Formatted energy amount
 */
function formatEnergy($amount) {
    return number_format($amount, 2) . ' kWh';
}

/**
 * Calculate time difference in minutes
 *
 * @param string $startTime Start time
 * @param string $endTime End time
 * @return int Time difference in minutes
 */
function timeDifferenceInMinutes($startTime, $endTime) {
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    $diff = $end->diff($start);

    return ($diff->h * 60) + $diff->i;
}

/**
 * Add minutes to a datetime
 *
 * @param string $dateTime Date and time string
 * @param int $minutes Minutes to add
 * @return string New date and time
 */
function addMinutesToDateTime($dateTime, $minutes) {
    $dt = new DateTime($dateTime);
    $dt->add(new DateInterval('PT' . $minutes . 'M'));
    return $dt->format('Y-m-d H:i:s');
}

/**
 * Check if a time slot is available
 *
 * @param int $chargingPointId Charging point ID
 * @param string $date Date in Y-m-d format
 * @param string $startTime Start time in H:i format
 * @param string $endTime End time in H:i format
 * @param int $excludeBookingId Booking ID to exclude from check (for updates)
 * @return bool True if the time slot is available, false otherwise
 */
function isTimeSlotAvailable($chargingPointId, $date, $startTime, $endTime, $excludeBookingId = null) {
    $params = [$chargingPointId, $date, $startTime, $endTime];

    $sql = "SELECT COUNT(*) as count FROM bookings 
            WHERE charging_point_id = ? 
            AND booking_date = ? 
            AND status IN ('scheduled', 'active')
            AND (
                (start_time <= ? AND end_time > ?) OR
                (start_time < ? AND end_time >= ?) OR
                (start_time >= ? AND end_time <= ?)
            )";

    if ($excludeBookingId) {
        $sql .= " AND id != ?";
        $params[] = $excludeBookingId;
    }

    $result = executeQuery($sql, array_merge($params, [$startTime, $startTime, $endTime, $endTime, $startTime, $endTime]));
    $row = $result->fetch_assoc();

    return $row['count'] == 0;
}

/**
 * Get available time slots for a specific date and charging point
 *
 * @param int $chargingPointId Charging point ID
 * @param string $date Date in Y-m-d format
 * @param int $durationMinutes Duration in minutes
 * @return array Array of available time slots
 */
function getAvailableTimeSlots($chargingPointId, $date, $durationMinutes = 60) {
    global $settings;

    // Get operating hours (6:00 AM to 10:00 PM)
    $operatingStart = 6 * 60; // 6:00 AM in minutes
    $operatingEnd = 22 * 60;  // 10:00 PM in minutes

    // Get interval in minutes
    $interval = $settings['min_booking_interval'];

    // Get all bookings for the charging point on the specified date
    $sql = "SELECT start_time, end_time FROM bookings 
            WHERE charging_point_id = ? 
            AND booking_date = ? 
            AND status IN ('scheduled', 'active')
            ORDER BY start_time";

    $bookings = fetchAll($sql, [$chargingPointId, $date]);

    // Convert booking times to minutes since midnight
    $bookedSlots = [];
    foreach ($bookings as $booking) {
        $startParts = explode(':', $booking['start_time']);
        $endParts = explode(':', $booking['end_time']);

        $startMinutes = ($startParts[0] * 60) + $startParts[1];
        $endMinutes = ($endParts[0] * 60) + $endParts[1];

        $bookedSlots[] = [
            'start' => $startMinutes,
            'end' => $endMinutes
        ];
    }

    // Find available slots
    $availableSlots = [];
    $current = $operatingStart;

    while ($current + $durationMinutes <= $operatingEnd) {
        $slotEnd = $current + $durationMinutes;
        $isAvailable = true;

        foreach ($bookedSlots as $bookedSlot) {
            // Check if the current slot overlaps with any booked slot
            if (($current < $bookedSlot['end'] && $slotEnd > $bookedSlot['start'])) {
                $isAvailable = false;
                // Jump to the end of this booked slot
                $current = $bookedSlot['end'];
                break;
            }
        }

        if ($isAvailable) {
            $startHour = floor($current / 60);
            $startMinute = $current % 60;
            $endHour = floor($slotEnd / 60);
            $endMinute = $slotEnd % 60;

            $availableSlots[] = [
                'start' => sprintf('%02d:%02d', $startHour, $startMinute),
                'end' => sprintf('%02d:%02d', $endHour, $endMinute)
            ];

            $current += $interval;
        }
    }

    return $availableSlots;
}

/**
 * Calculate the cost of charging
 *
 * @param float $energyConsumed Energy consumed in kWh
 * @return float Cost in currency
 */
function calculateChargingCost($energyConsumed) {
    global $settings;

    return $energyConsumed * $settings['price_per_kwh'];
}

/**
 * Check for booking expiration
 *
 * This function should be called periodically to check for expired bookings
 * and mark them as no-shows if the user hasn't started charging within the
 * expiry time limit.
 */
function checkBookingExpirations() {
    global $settings;

    $expiryMinutes = $settings['booking_expiry_minutes'];
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    $sql = "SELECT b.*, u.id as user_id 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            WHERE b.booking_date = ? 
            AND b.status = 'scheduled'
            AND TIMESTAMPDIFF(MINUTE, 
                CONCAT(b.booking_date, ' ', b.start_time), 
                CONCAT(?, ' ', ?)) > ?";

    $expiredBookings = fetchAll($sql, [$currentDate, $currentDate, $currentTime, $expiryMinutes]);

    foreach ($expiredBookings as $booking) {
        // Update booking status to no_show
        update('bookings', ['status' => 'no_show'], 'id = ?', [$booking['id']]);

        // Update charging point status to available
        update('charging_points', ['status' => 'available'], 'id = ?', [$booking['charging_point_id']]);
    }
}