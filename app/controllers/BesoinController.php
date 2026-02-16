<?php
require_once __DIR__ . '/../repositories/BesoinRepository.php';

class BesoinController {
    private BesoinRepository $besoinRepository;

    public function __construct() {
        try {
            $db = Flight::db();
        } catch (\Throwable $e) {
            throw new \RuntimeException('Database connection is not available.', 0, $e);
        }

        if ($db === null) {
            throw new \RuntimeException('Database connection is not available.');
        }

        $this->besoinRepository = new BesoinRepository($db);
    }

    public function showListeBesoin(): void {
        try {
            $listeBesoins = $this->besoinRepository->getVillesAvecBesoinsPaType();
            
            Flight::render('besoins/besoin-liste', [
                'besoins' => $listeBesoins,
                'title' => 'Liste des Besoins'
            ]);
        } catch (Exception $e) {
            Flight::notFound();
            echo 'Erreur: ' . $e->getMessage();
        }
    }

  
}