<?php
// Configurazione database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ev_charging_db');

/**
 * Inizializza il database se non esiste
 */
function initializeDatabase() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");

    if ($result->num_rows === 0) {
        $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $conn->select_db(DB_NAME);

        $sql = file_get_contents(dirname(__DIR__) . '/supabase/migrations/schema.sql');
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $conn->query($statement);
            }
        }

        $conn->close();
        return true;
    }

    $conn->close();
    return false;
}

/**
 * Restituisce una connessione PDO
 */
function getDbConnection() {
    static $pdo;

    if (!$pdo) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}

/**
 * Esegue una query preparata
 */
function executeQuery($sql, $params = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Ritorna un singolo record
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Ritorna tutti i record
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Inserisce un record
 */
function insert($table, $data) {
    try {
        $pdo = getDbConnection();

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $values = array_values($data);

        $sql = "INSERT INTO `$table` (" . implode(', ', $columns) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        return $pdo->lastInsertId();

    } catch (PDOException $e) {
        error_log("❌ INSERT ERROR in table `$table`: " . $e->getMessage());
        return false;
    }
}


/**
 * Aggiorna un record
 */
function update($table, $data, $where, $whereParams = []) {
    $sets = [];
    $values = [];

    foreach ($data as $column => $value) {
        $sets[] = "$column = ?";
        $values[] = $value;
    }

    $sql = "UPDATE $table SET " . implode(', ', $sets) . " WHERE $where";
    $params = array_merge($values, $whereParams);

    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount() > 0;
}

/**
 * Elimina un record
 */
function delete($table, $where, $params = []) {
    $sql = "DELETE FROM $table WHERE $where";
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount() > 0;
}
