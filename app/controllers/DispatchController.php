<?php

require_once __DIR__ . '/../repositories/DispatchRepository.php';
require_once __DIR__ . '/../repositories/AchatRepository.php';
require_once __DIR__ . '/../repositories/ConfigurationRepository.php';

class DispatchController {
    private PDO $db;
    private DispatchRepository $dispatchRepo;

    public function __construct() {
        $this->db = Flight::db();
        $this->dispatchRepo = new DispatchRepository($this->db);
    }

    // Affiche la page de dispatch avec les attributions existantes
    public function show() {
        $attributions = $this->dispatchRepo->obtenirAttributions();

        Flight::render('dispatch', [
            'attributions' => $attributions,
            'summary' => [],
            'leftDons' => [],
            'leftBesoins' => [],
            'error' => null,
            'debug' => []
        ]);
    }

    // Réinitialise les attributions
    public function reset() {
        try {
            $this->dispatchRepo->resetAttributions();
            Flight::redirect('/distributions');
        } catch (Exception $e) {
            Flight::render('dispatch', ['error' => 'Erreur: ' . $e->getMessage(), 'summary' => [], 'leftDons' => [], 'leftBesoins' => []]);
        }
    }

    // Calcule et retourne la simulation du dispatch (sans enregistrer)
    private function calculerDispatch($commit = false) {
        $summary = [];
        $debug = [];

        try {
            if ($commit) {
                $this->dispatchRepo->beginTransaction();
            }

            $types = $this->dispatchRepo->obtenirTypesBesoins();

            foreach ($types as $type) {
                $typeId = (int) $type['id'];

                $besoins = $this->dispatchRepo->obtenirBesoinsNonSatisfaitsParType($typeId);
                $dons = $this->dispatchRepo->obtenirDonsDisponiblesParType($typeId);
                
                $debug[] = "Type {$type['nom']} (ID={$typeId}): " . count($besoins) . " besoins, " . count($dons) . " dons";

                $donIndex = 0;

                foreach ($besoins as $besoin) {
                    $besoinId = (int)$besoin['id'];
                    $needed = (int)$besoin['quantite'] - (int)$besoin['recu'];

                    while ($needed > 0 && $donIndex < count($dons)) {
                        $don = $dons[$donIndex];
                        $available = (int)$don['quantite'] - (int)$don['attrib'];

                        if ($available <= 0) {
                            $donIndex++;
                            continue;
                        }

                        $assign = min($needed, $available);

                        if ($commit) {
                            $this->dispatchRepo->creerAttribution((int)$don['id'], $besoinId, $assign);
                        }

                        $summary[] = [
                            'type' => $type['nom'],
                            'don_description' => "Don: " . $don['nom'] . " (" . (int)$don['quantite'] . " unités)",
                            'besoin_description' => "Besoin: " . $besoin['ville_nom'] . " - " . $type['nom'] . " (" . (int)$besoin['quantite'] . " unités)",
                            'quantite' => $assign,
                            'besoin_date' => $besoin['date_saisie'],
                            'ville_nom' => $besoin['ville_nom']
                        ];

                        $needed -= $assign;
                        $dons[$donIndex]['attrib'] = (int)$dons[$donIndex]['attrib'] + $assign;

                        if (((int)$dons[$donIndex]['quantite'] - (int)$dons[$donIndex]['attrib']) <= 0) {
                            $donIndex++;
                        }
                    }
                }
            }

            if ($commit) {
                $this->dispatchRepo->commit();
            }

            return ['summary' => $summary, 'debug' => $debug, 'error' => null];

        } catch (Exception $e) {
            if ($commit) {
                $this->dispatchRepo->rollBack();
            }
            return ['summary' => [], 'debug' => $debug, 'error' => $e->getMessage()];
        }
    }

    // Simule le dispatch sans enregistrer
    public function simulate() {
        $result = $this->calculerDispatch(false);
        
        $leftDons = $this->dispatchRepo->obtenirDonsRestants();
        $leftBesoins = $this->dispatchRepo->obtenirBesoinsRestants();

        Flight::render('dispatch', [
            'summary' => $result['summary'],
            'leftDons' => $leftDons,
            'leftBesoins' => $leftBesoins,
            'error' => $result['error'],
            'debug' => $result['debug'],
            'is_simulation' => true
        ]);
    }

    // Valide et enregistre le dispatch
    public function validate() {
        $result = $this->calculerDispatch(true);
        Flight::redirect('/besoins-restants');
    }

    // Affiche les besoins restants
    public function showLeftovers() {
        $leftBesoins = $this->dispatchRepo->obtenirBesoinsRestants();

        // Récupère les dons en argent disponibles
        $achatRepo = new AchatRepository($this->db);
        $donsArgent = $achatRepo->obtenirDonsArgentDisponibles();

        // Récupère les achats en cours
        $achatsEnCours = $achatRepo->obtenirEnCours();

        // Récupère le taux de frais
        $configRepo = new ConfigurationRepository($this->db);
        $tauxFrais = $configRepo->obtenirTauxFraisAchat();

        // Messages d'erreur/succès de la session
        $erreur = $_GET['erreur'] ?? null;
        $succes = $_GET['succes'] ?? null;

        Flight::render('besoins_restants', [
            'leftBesoins' => $leftBesoins,
            'donsArgent' => $donsArgent,
            'achatsEnCours' => $achatsEnCours,
            'tauxFrais' => $tauxFrais,
            'erreur' => $erreur,
            'succes' => $succes
        ]);
    }
}
