<?php

require_once __DIR__ . '/../repositories/TypeBesoinRepository.php';
require_once __DIR__ . '/../repositories/DonRepository.php';
require_once __DIR__ . '/../repositories/DashboardRepository.php';
require_once __DIR__ . '/../repositories/BesoinRepository.php';
require_once __DIR__ . '/../repositories/CategorieRepository.php';
require_once __DIR__ . '/../controllers/DonController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/BesoinController.php';

require_once __DIR__ . '/../controllers/DispatchController.php';



Flight::route('GET /', function() {
    Flight::render('home');
});

Flight::route('GET /dons/saisie', [new DonController(), 'showForm']);
Flight::route('POST /dons/saisie', [new DonController(), 'store']);
Flight::route('GET /dons/liste', [new DonController(), 'list']);
Flight::route('GET /dashboard', [new DashboardController(), 'index']);

Flight::route('GET /distributions', [new DispatchController(), 'show']);
Flight::route('GET /dispatch/run', [new DispatchController(), 'index']);



// Routes Besoins
Flight::route('GET /besoins', [new BesoinController(), 'showListeBesoin']);
Flight::route('GET /besoins/ajouter', [new BesoinController(), 'showAjoutBesoin']);
Flight::route('POST /besoins/ajouter', [new BesoinController(), 'storeBesoin']);