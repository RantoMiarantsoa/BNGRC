<?php

require_once __DIR__ . '/../repositories/TypeBesoinRepository.php';
require_once __DIR__ . '/../repositories/DonRepository.php';
require_once __DIR__ . '/../repositories/DashboardRepository.php';
require_once __DIR__ . '/../repositories/BesoinRepository.php';
require_once __DIR__ . '/../controllers/DonController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/BesoinController.php';

Flight::route('GET /', function() {
    Flight::render('home');
});

Flight::route('GET /dons/saisie', [new DonController(), 'showForm']);
Flight::route('POST /dons/saisie', [new DonController(), 'store']);
Flight::route('GET /dons/liste', [new DonController(), 'list']);
Flight::route('GET /dashboard', [new DashboardController(), 'index']);

// Routes Besoins
Flight::route('GET /besoins', [new BesoinController(), 'showListeBesoin']);
Flight::route('GET /besoins/ajouter', [new BesoinController(), 'showAjoutBesoin']);
Flight::route('POST /besoins/ajouter', [new BesoinController(), 'storeBesoin']);