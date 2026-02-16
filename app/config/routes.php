<?php
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/ObjetController.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/CategorieRepository.php';
require_once __DIR__ . '/../repositories/ObjetRepository.php';
require_once __DIR__ . '/../repositories/ImageRepository.php';

// Authentication / home
Flight::route('GET /', ['AdminController', 'showLogin']);
Flight::route('POST /login', ['AdminController', 'postLogin']);
Flight::route('GET /home', ['AdminController', 'showHome']);
Flight::route('GET /logout', ['AdminController', 'logout']);


Flight::route('GET /objet/form', function() {
    $pdo = Flight::db();
    $repo = new CategorieRepository($pdo);
    $categories = $repo->getAll();
    Flight::render('auth/formulaireObjet', ['categories' => $categories]);
});

Flight::route('POST /objet/ajouter', ['ObjetController', 'postAjouter']);

Flight::route('GET /objet/list', function() {
    $pdo = Flight::db();
    $repo = new ObjetController();
    $objets = $repo->getObjetByUserConnected(1);

    $categoriesRepo = new CategorieRepository($pdo);
    
Flight::render('auth/listObjet', ['objets' => $objets]);
});