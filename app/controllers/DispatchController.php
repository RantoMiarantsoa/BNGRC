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
            $attributions = $this->dispatchRepo->obtenirAttributions();
            Flight::render('dispatch', [
                'attributions' => $attributions,
                'summary' => [],
                'leftDons' => [],
                'leftBesoins' => [],
                'error' => null,
                'debug' => [],
                'succes' => 'Attributions réinitialisées'
            ]);
        } catch (Exception $e) {
            Flight::render('dispatch', ['error' => 'Erreur: ' . $e->getMessage(), 'summary' => [], 'leftDons' => [], 'leftBesoins' => []]);
        }
    }

    // Calcule et retourne la simulation du dispatch (sans enregistrer)
    private function calculerDispatch($commit = false, string $strategy = 'oldest') {
        // Si stratégie proportionnelle, utiliser une méthode dédiée
        if ($strategy === 'proportional') {
            return $this->calculerDispatchProportionnel($commit);
        }

        $summary = [];
        $debug = [];
        $donsUtilises = []; // Track des dons utilisés: don_id => quantite_attribuee_simulation

        try {
            if ($commit) {
                $this->dispatchRepo->beginTransaction();
            }

            // Itérer par type de besoin (pas par catégorie) pour une correspondance exacte
            $types = $this->dispatchRepo->obtenirTypesBesoins();

            foreach ($types as $type) {
                $typeId = (int) $type['id'];
                $typeNom = $type['nom'];

                $besoins = $this->dispatchRepo->obtenirBesoinsNonSatisfaitsParType($typeId, $strategy);
                // Chercher les dons correspondant au même nom de type
                $dons = $this->dispatchRepo->obtenirDonsDisponiblesParNomType($typeNom);
                
                $debug[] = "Type {$typeNom} (catégorie: {$type['categorie_nom']}): " . count($besoins) . " besoins, " . count($dons) . " dons";

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

                        // Tracker les dons utilisés pour la simulation
                        $donId = (int)$don['id'];
                        if (!isset($donsUtilises[$donId])) {
                            $donsUtilises[$donId] = [
                                'id' => $donId,
                                'nom' => $don['nom'],
                                'quantite' => (int)$don['quantite'],
                                'attrib_avant' => (int)$don['attrib'],
                                'attrib_simulation' => 0
                            ];
                        }
                        $donsUtilises[$donId]['attrib_simulation'] += $assign;

                        $summary[] = [
                            'type' => $type['categorie_nom'],
                            'don_description' => "Don: " . $don['nom'] . " (" . (int)$don['quantite'] . " unités)",
                            'besoin_description' => "Besoin: " . $besoin['ville_nom'] . " - " . $typeNom . " (" . (int)$besoin['quantite'] . " unités)",
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

            // Calculer les dons restants après simulation
            $leftDonsSimulation = $this->calculerDonsRestantsSimulation($donsUtilises);

            return ['summary' => $summary, 'debug' => $debug, 'error' => null, 'leftDons' => $leftDonsSimulation];

        } catch (Exception $e) {
            if ($commit) {
                $this->dispatchRepo->rollBack();
            }
            return ['summary' => [], 'debug' => $debug, 'error' => $e->getMessage(), 'leftDons' => []];
        }
    }

    /**
     * Calcule les dons restants après simulation
     * Prend en compte les attributions simulées uniquement
     */
    private function calculerDonsRestantsSimulation(array $donsUtilises): array {
        // Récupérer tous les dons avec leurs attributions actuelles
        $tousLesDons = $this->dispatchRepo->obtenirTousLesDons();
        
        $result = [];
        foreach ($tousLesDons as $don) {
            $donId = (int)$don['id'];
            $quantiteTotale = (int)$don['quantite'];
            // Quantité attribuée dans cette simulation uniquement
            $attribSimulation = isset($donsUtilises[$donId]) ? $donsUtilises[$donId]['attrib_simulation'] : 0;
            // Quantité disponible avant simulation (quantité totale - attributions existantes)
            $disponibleAvant = $quantiteTotale - (int)$don['attrib'];
            // Reste après simulation
            $resteApresSimulation = $disponibleAvant - $attribSimulation;
            
            // Inclure tous les dons qui ont du stock disponible pour cette simulation
            if ($disponibleAvant > 0) {
                $result[] = [
                    'id' => $donId,
                    'nom' => $don['nom'],
                    'quantite' => $disponibleAvant, // Quantité disponible avant simulation
                    'attrib' => $attribSimulation,  // Ce qui serait attribué par cette simulation
                    'date_saisie' => $don['date_saisie']
                ];
            }
        }
        
        return $result;
    }

    /**
     * Distribution proportionnelle :
     * Pour chaque type de besoin, répartit les dons proportionnellement
     * aux besoins de chaque ville.
     * Formule: quantité_ville = (besoin_ville / somme_besoins) × quantité_don_disponible
     */
    private function calculerDispatchProportionnel($commit = false) {
        $summary = [];
        $debug = [];
        $donsUtilises = []; // Track des dons utilisés

        try {
            if ($commit) {
                $this->dispatchRepo->beginTransaction();
            }

            $types = $this->dispatchRepo->obtenirTypesBesoins();

            foreach ($types as $type) {
                $typeId = (int) $type['id'];
                $typeNom = $type['nom'];

                // Récupérer tous les besoins pour ce type
                $besoins = $this->dispatchRepo->obtenirBesoinsNonSatisfaitsParType($typeId, 'oldest');
                // Chercher les dons correspondant au même nom
                $dons = $this->dispatchRepo->obtenirDonsDisponiblesParNomType($typeNom);

                if (empty($besoins) || empty($dons)) {
                    continue;
                }

                // Calculer le total des besoins restants
                $totalBesoinsRestants = 0;
                foreach ($besoins as $besoin) {
                    $totalBesoinsRestants += (int)$besoin['quantite'] - (int)$besoin['recu'];
                }

                if ($totalBesoinsRestants <= 0) {
                    continue;
                }

                // Calculer le total des dons disponibles
                $totalDonsDisponibles = 0;
                foreach ($dons as $don) {
                    $totalDonsDisponibles += (int)$don['quantite'] - (int)$don['attrib'];
                }

                $debug[] = "Type {$typeNom} (catégorie: {$type['categorie_nom']}): " . count($besoins) . " besoins (total: {$totalBesoinsRestants}), " . count($dons) . " dons (total: {$totalDonsDisponibles})";

                // Si pas assez de dons, on répartit proportionnellement
                // Si assez de dons, chaque besoin reçoit exactement ce qu'il lui faut
                $quantiteADistribuer = min($totalDonsDisponibles, $totalBesoinsRestants);

                if ($quantiteADistribuer <= 0) {
                    continue;
                }

                // Calculer la part pour chaque besoin
                $attributionsCalculees = [];
                $totalAttribue = 0;

                foreach ($besoins as $index => $besoin) {
                    $besoinRestant = (int)$besoin['quantite'] - (int)$besoin['recu'];
                    
                    // Part proportionnelle: (besoin_ville / total_besoins) × quantité_à_distribuer
                    $partProportionnelle = ($besoinRestant / $totalBesoinsRestants) * $quantiteADistribuer;
                    
                    // Arrondir à l'entier inférieur (floor) pour éviter d'attribuer plus que disponible
                    $quantiteAttribuee = (int) floor($partProportionnelle);
                    
                    // Ne pas attribuer plus que le besoin réel
                    $quantiteAttribuee = min($quantiteAttribuee, $besoinRestant);

                    if ($quantiteAttribuee > 0) {
                        $attributionsCalculees[] = [
                            'besoin' => $besoin,
                            'quantite' => $quantiteAttribuee
                        ];
                        $totalAttribue += $quantiteAttribuee;
                    }
                }

                // Distribuer le reste (arrondi) au premier besoin qui peut encore recevoir
                $reste = $quantiteADistribuer - $totalAttribue;
                foreach ($attributionsCalculees as &$attribution) {
                    if ($reste <= 0) break;
                    $besoinRestant = (int)$attribution['besoin']['quantite'] - (int)$attribution['besoin']['recu'];
                    $peutRecevoirEnPlus = $besoinRestant - $attribution['quantite'];
                    if ($peutRecevoirEnPlus > 0) {
                        $ajout = min($reste, $peutRecevoirEnPlus);
                        $attribution['quantite'] += $ajout;
                        $reste -= $ajout;
                    }
                }
                unset($attribution);

                // Maintenant attribuer les dons aux besoins
                $donIndex = 0;
                $donRestant = 0;
                $donCourant = null;

                foreach ($attributionsCalculees as $attribution) {
                    $besoin = $attribution['besoin'];
                    $quantiteAAlouer = $attribution['quantite'];

                    while ($quantiteAAlouer > 0 && $donIndex < count($dons)) {
                        if ($donRestant <= 0) {
                            $donCourant = $dons[$donIndex];
                            $donRestant = (int)$donCourant['quantite'] - (int)$donCourant['attrib'];
                            if ($donRestant <= 0) {
                                $donIndex++;
                                continue;
                            }
                        }

                        $assign = min($quantiteAAlouer, $donRestant);

                        if ($commit) {
                            $this->dispatchRepo->creerAttribution((int)$donCourant['id'], (int)$besoin['id'], $assign);
                        }

                        // Tracker les dons utilisés pour la simulation
                        $donId = (int)$donCourant['id'];
                        if (!isset($donsUtilises[$donId])) {
                            $donsUtilises[$donId] = [
                                'id' => $donId,
                                'nom' => $donCourant['nom'],
                                'quantite' => (int)$donCourant['quantite'],
                                'attrib_avant' => (int)$donCourant['attrib'],
                                'attrib_simulation' => 0
                            ];
                        }
                        $donsUtilises[$donId]['attrib_simulation'] += $assign;

                        $summary[] = [
                            'type' => $type['categorie_nom'],
                            'don_description' => "Don: " . $donCourant['nom'] . " (" . (int)$donCourant['quantite'] . " unités)",
                            'besoin_description' => "Besoin: " . $besoin['ville_nom'] . " - " . $typeNom . " (" . (int)$besoin['quantite'] . " unités)",
                            'quantite' => $assign,
                            'besoin_date' => $besoin['date_saisie'],
                            'ville_nom' => $besoin['ville_nom']
                        ];

                        $quantiteAAlouer -= $assign;
                        $donRestant -= $assign;
                        $dons[$donIndex]['attrib'] = (int)$dons[$donIndex]['attrib'] + $assign;

                        if ($donRestant <= 0) {
                            $donIndex++;
                        }
                    }
                }
            }

            if ($commit) {
                $this->dispatchRepo->commit();
            }

            // Calculer les dons restants après simulation
            $leftDonsSimulation = $this->calculerDonsRestantsSimulation($donsUtilises);

            return ['summary' => $summary, 'debug' => $debug, 'error' => null, 'leftDons' => $leftDonsSimulation];

        } catch (Exception $e) {
            if ($commit) {
                $this->dispatchRepo->rollBack();
            }
            return ['summary' => [], 'debug' => $debug, 'error' => $e->getMessage(), 'leftDons' => []];
        }
    }

    // Simule le dispatch sans enregistrer
    public function simulate() {
        $strategy = Flight::request()->query->strategy ?? 'oldest';
        if (!in_array($strategy, ['oldest', 'smallest', 'proportional'])) $strategy = 'oldest';

        $result = $this->calculerDispatch(false, $strategy);
        
        // Utiliser les dons restants calculés par la simulation
        $leftDons = $result['leftDons'] ?? [];
        $leftBesoins = $this->dispatchRepo->obtenirBesoinsRestants();

        Flight::render('dispatch', [
            'summary' => $result['summary'],
            'leftDons' => $leftDons,
            'leftBesoins' => $leftBesoins,
            'error' => $result['error'],
            'debug' => $result['debug'],
            'is_simulation' => true,
            'strategy' => $strategy
        ]);
    }

    // Valide et enregistre le dispatch
    public function validate() {
        $strategy = Flight::request()->query->strategy ?? 'oldest';
        if (!in_array($strategy, ['oldest', 'smallest', 'proportional'])) $strategy = 'oldest';

        $result = $this->calculerDispatch(true, $strategy);

        $attributions = $this->dispatchRepo->obtenirAttributions();
        $leftDons = $this->dispatchRepo->obtenirDonsRestants();

        Flight::render('dispatch', [
            'attributions' => $attributions,
            'summary' => $result['summary'],
            'leftDons' => $leftDons,
            'leftBesoins' => [],
            'error' => $result['error'],
            'debug' => $result['debug'],
            'strategy' => $strategy,
            'succes' => 'Attributions validées et enregistrées avec succès'
        ]);
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
