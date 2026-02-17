<?php
require_once __DIR__ . '/config.php';

// Start session for simple auto-login and message tracking
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL for the app (root or subfolder)
Flight::set('flight.base_url', BASE_URL);

// Enregistrement de la base de données
Flight::register('db', 'PDO', array(
    DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
    DB_USER,
    DB_PASS,
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    )
));

// **Définir le chemin des vues AVANT de charger les routes**
Flight::set('flight.views.path', __DIR__ . '/../views');

// Charger les routes
require_once __DIR__ . '/routes.php';
