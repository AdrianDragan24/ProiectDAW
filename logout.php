<?php
session_start();

// Golește variabilele din sesiune
$_SESSION = [];

// Șterge cookie-ul PHPSESSID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Distruge sesiunea
session_destroy();

// Redirect
header("Location: autentificare.php");
exit;
