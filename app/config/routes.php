<?php

require_once __DIR__ . '/../repositories/TypeBesoinRepository.php';
require_once __DIR__ . '/../repositories/DonRepository.php';
require_once __DIR__ . '/../repositories/DashboardRepository.php';
require_once __DIR__ . '/../repositories/BesoinRepository.php';
require_once __DIR__ . '/../repositories/ConfigurationRepository.php';
require_once __DIR__ . '/../repositories/AchatRepository.php';
require_once __DIR__ . '/../repositories/AttributionRepository.php';
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

// Routes Achats
Flight::route('GET /achats/besoins-restants', [new AchatController(Flight::db()), 'afficherBesoinsRestants']);
Flight::route('GET /achats/saisie/@besoin_id', [new AchatController(Flight::db()), 'afficherSaisieAchat']);
Flight::route('POST /achats/creer', [new AchatController(Flight::db()), 'creerAchat']);
Flight::route('GET /achats/liste', [new AchatController(Flight::db()), 'afficherListeAchats']);
Flight::route('POST /achats/finaliser/@achat_id', [new AchatController(Flight::db()), 'finaliserAchat']);
Flight::route('POST /achats/annuler/@achat_id', [new AchatController(Flight::db()), 'annulerAchat']);