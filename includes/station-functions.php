<?php
/**
 * Station related functions
 */

/**
 * Get all stations
 *
 * @param bool $activeOnly If true, return only active stations (currently unused)
 * @return array Array of stations
 */
function getAllStations($activeOnly = true) {
    $sql = "SELECT * FROM Stations";
    return fetchAll($sql, []);
}

/**
 * Get station details
 *
 * @param int $stationId Station ID
 * @return array|null Station details or null if not found
 */
function getStationDetails($stationId) {
    return fetchOne("SELECT * FROM Stations WHERE station_id = ?", [$stationId]);
}

/**
 * Get all charging points for a station
 *
 * @param int $stationId Station ID
 * @return array Array of charging points
 */
function getStationChargingPoints($stationId) {
    $sql = "SELECT *, 
            CASE 
                WHEN occupied = 0 THEN 'available'
                WHEN occupied = 1 THEN 'not-available'
            END as status
            FROM Charging_Points 
            WHERE station_id = ?";
    return fetchAll($sql, [$stationId]);
}

/**
 * Get charging point details
 *
 * @param int $chargingPointId Charging point ID
 * @return array|null Charging point details with station info or null if not found
 */
function getChargingPointDetails($chargingPointId) {
    $sql = "SELECT cp.*, 
                   s.station_id, 
                   s.address_street, 
                   s.address_city,
                   s.address_municipality, 
                   s.address_civic_num, 
                   s.address_zipcode,
                   CASE 
                       WHEN cp.occupied = 0 THEN 'available'
                       WHEN cp.occupied = 1 THEN 'not-available'
                   END as status
            FROM Charging_Points cp
            JOIN Stations s ON cp.station_id = s.station_id
            WHERE cp.charging_point_id = ?";

    return fetchOne($sql, [$chargingPointId]);
}

/**
 * Get available charging points at a station
 *
 * @param int $stationId Station ID
 * @return array Array of available charging points
 */
function getAvailableChargingPoints($stationId) {
    $sql = "SELECT * FROM Charging_Points 
            WHERE station_id = ? AND occupied = 0";
    return fetchAll($sql, [$stationId]);
}