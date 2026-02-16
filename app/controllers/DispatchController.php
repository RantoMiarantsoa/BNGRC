<?php

require_once __DIR__ . '/../repositories/DonRepository.php';
require_once __DIR__ . '/../repositories/BesoinRepository.php';

class DispatchController {
    private PDO $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    // Affiche la page de dispatch avec les attributions existantes
    public function show() {
        // Récupérer les attributions existantes
        $attributions = $this->db->query(
            "SELECT a.id, a.quantite_attribuee, a.date_dispatch,
                    d.nom AS don_nom, d.quantite AS don_quantite,
                    b.quantite AS besoin_quantite, b.date_saisie AS besoin_date,
                    t.nom AS type_nom, v.nom AS ville_nom
             FROM bngrc_attribution a
             JOIN bngrc_don d ON d.id = a.don_id
             JOIN bngrc_besoin b ON b.id = a.besoin_id
             JOIN bngrc_type_besoin t ON t.id = b.type_besoin_id
             JOIN bngrc_ville v ON v.id = b.ville_id
             ORDER BY a.date_dispatch DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

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
            $this->db->exec('TRUNCATE TABLE bngrc_attribution');
            Flight::redirect('/distributions');
        } catch (Exception $e) {
            Flight::render('dispatch', ['error' => 'Erreur: ' . $e->getMessage(), 'summary' => [], 'leftDons' => [], 'leftBesoins' => []]);
        }
    }

    // Calcule et retourne la simulation du dispatch (sans enregistrer)
    private function calculerDispatch($commit = false) {
        $db = $this->db;
        $summary = [];
        $debug = [];

        try {
            if ($commit) {
                $db->beginTransaction();
            }

            // Récupérer tous les types
            $typesStmt = $db->query('SELECT id, nom FROM bngrc_type_besoin');
            $types = $typesStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($types as $type) {
                $typeId = (int) $type['id'];

                // Besoins non satisfaits, ordonnés par date (FIFO)
                $besoinsStmt = $db->prepare(
                    "SELECT b.id, b.quantite, b.date_saisie, b.ville_id, v.nom AS ville_nom, COALESCE(SUM(a.quantite_attribuee),0) AS recu
                     FROM bngrc_besoin b
                     LEFT JOIN bngrc_ville v ON v.id = b.ville_id
                     LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
                     WHERE b.type_besoin_id = ?
                     GROUP BY b.id
                     HAVING b.quantite > recu
                     ORDER BY b.date_saisie ASC"
                );
                $besoinsStmt->execute([$typeId]);
                $besoins = $besoinsStmt->fetchAll(PDO::FETCH_ASSOC);

                // Dons disponibles pour ce type, ordonnés par date (FIFO)
                $donsStmt = $db->prepare(
                    "SELECT d.id, d.nom, d.quantite, d.date_saisie, COALESCE(SUM(a.quantite_attribuee),0) AS attrib
                     FROM bngrc_don d
                     LEFT JOIN bngrc_attribution a ON a.don_id = d.id
                     LEFT JOIN bngrc_categorie c ON c.id = d.id_type_categorie
                     LEFT JOIN bngrc_type_besoin t ON t.categorie_id = c.id
                     WHERE t.id = ?
                     GROUP BY d.id
                     HAVING d.quantite > attrib
                     ORDER BY d.date_saisie ASC"
                );
                $donsStmt->execute([$typeId]);
                $dons = $donsStmt->fetchAll(PDO::FETCH_ASSOC);
                
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

                        // Enregistrer l'attribution seulement si commit = true
                        if ($commit) {
                            $ins = $db->prepare('INSERT INTO bngrc_attribution (don_id, besoin_id, quantite_attribuee) VALUES (?, ?, ?)');
                            $ins->execute([(int)$don['id'], $besoinId, $assign]);
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
                        // Mettre à jour l'attrib localement
                        $dons[$donIndex]['attrib'] = (int)$dons[$donIndex]['attrib'] + $assign;

                        if (((int)$dons[$donIndex]['quantite'] - (int)$dons[$donIndex]['attrib']) <= 0) {
                            $donIndex++;
                        }
                    }
                }
            }

            if ($commit) {
                $db->commit();
            }

            return ['summary' => $summary, 'debug' => $debug, 'error' => null];

        } catch (Exception $e) {
            if ($commit) {
                $db->rollBack();
            }
            return ['summary' => [], 'debug' => $debug, 'error' => $e->getMessage()];
        }
    }

    // Simule le dispatch sans enregistrer
    public function simulate() {
        $result = $this->calculerDispatch(false);
        
        // Calculer les restes après simulation
        $leftDons = $this->db->query(
            "SELECT d.id, d.nom, d.quantite, COALESCE(SUM(a.quantite_attribuee),0) AS attrib, d.date_saisie
             FROM bngrc_don d
             LEFT JOIN bngrc_attribution a ON a.don_id = d.id
             GROUP BY d.id
             HAVING d.quantite > attrib
             ORDER BY d.date_saisie ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $leftBesoins = $this->db->query(
            "SELECT b.id, b.type_besoin_id, t.nom AS type_nom, v.nom AS ville_nom, b.quantite, COALESCE(SUM(a.quantite_attribuee),0) AS recu, b.date_saisie
             FROM bngrc_besoin b
             LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
             LEFT JOIN bngrc_type_besoin t ON t.id = b.type_besoin_id
             LEFT JOIN bngrc_ville v ON v.id = b.ville_id
             GROUP BY b.id
             HAVING b.quantite > recu
             ORDER BY b.date_saisie ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

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
        
        // Rediriger vers la page des besoins restants après validation
        Flight::redirect('/besoins-restants');
    }

    // Affiche les besoins restants
    public function showLeftovers() {
        $leftBesoins = $this->db->query(
            "SELECT b.id, b.type_besoin_id, t.nom AS type_nom, v.nom AS ville_nom, b.quantite, COALESCE(SUM(a.quantite_attribuee),0) AS recu, b.date_saisie
             FROM bngrc_besoin b
             LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
             LEFT JOIN bngrc_type_besoin t ON t.id = b.type_besoin_id
             LEFT JOIN bngrc_ville v ON v.id = b.ville_id
             GROUP BY b.id
             HAVING b.quantite > recu
             ORDER BY b.date_saisie ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        Flight::render('besoins_restants', ['leftBesoins' => $leftBesoins]);
    }
}
