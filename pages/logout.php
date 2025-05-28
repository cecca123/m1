<?php
// Inizio la sessione se non è già attiva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Includo i file necessari
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

// Distruggo tutte le variabili di sessione
$_SESSION = array();

// Se si desidera distruggere completamente la sessione, cancellare anche il cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Distruggo la sessione
session_destroy();

// Registro il logout
error_log("User logged out - Session destroyed");

// Reindirizzo alla pagina di login con un messaggio di successo
setFlashMessage('success', 'You have been successfully logged out.');
redirect('index.php');

// Termino lo script
exit();