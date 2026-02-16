<?php

require_once __DIR__ . '/../repositories/DonRepository.php';
require_once __DIR__ . '/../repositories/BesoinRepository.php';

class DispatchController {
    private PDO $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    // Affiche la page de dispatch sans exécuter l'algorithme
    public function show() {
        Flight::render('dispatch', ['summary' => [], 'leftDons' => [], 'leftBesoins' => [], 'error' => null]);
    }

    // Exécute l'algorithme FIFO et affiche le résumé
    public function index() {
        $db = $this->db;
        $summary = [];

        try {
            $db->beginTransaction();

            // Récupérer tous les types
            $typesStmt = $db->query('SELECT id, nom FROM bngrc_type_besoin');
            $types = $typesStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($types as $type) {
                $typeId = (int) $type['id'];

                // Besoins non satisfaits, ordonnés par date (FIFO)
                $besoinsStmt = $db->prepare(
                    "SELECT b.id, b.quantite, b.date_saisie, COALESCE(SUM(a.quantite_attribuee),0) AS recu
                     FROM bngrc_besoin b
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
                    "SELECT d.id, d.quantite, d.date_saisie, COALESCE(SUM(a.quantite_attribuee),0) AS attrib
                     FROM bngrc_don d
                     LEFT JOIN bngrc_attribution a ON a.don_id = d.id
                     WHERE d.type_besoin_id = ?
                     GROUP BY d.id
                     HAVING d.quantite > attrib
                     ORDER BY d.date_saisie ASC"
                );
                $donsStmt->execute([$typeId]);
                $dons = $donsStmt->fetchAll(PDO::FETCH_ASSOC);

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

                        $ins = $db->prepare('INSERT INTO bngrc_attribution (don_id, besoin_id, quantite_attribuee) VALUES (?, ?, ?)');
                        $ins->execute([(int)$don['id'], $besoinId, $assign]);

                        $summary[] = [
                            'type' => $type['nom'],
                            'don_id' => (int)$don['id'],
                            'besoin_id' => $besoinId,
                            'quantite' => $assign
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

            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            Flight::render('dispatch', ['error' => $e->getMessage(), 'summary' => [], 'leftDons' => [], 'leftBesoins' => []]);
            return;
        }

        // Calculer restes après dispatch
        $leftDons = $db->query(
            "SELECT d.id, d.type_besoin_id, t.nom AS type_nom, d.quantite, COALESCE(SUM(a.quantite_attribuee),0) AS attrib, d.date_saisie
             FROM bngrc_don d
             LEFT JOIN bngrc_attribution a ON a.don_id = d.id
             LEFT JOIN bngrc_type_besoin t ON t.id = d.type_besoin_id
             GROUP BY d.id
             HAVING d.quantite > attrib
             ORDER BY d.date_saisie ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $leftBesoins = $db->query(
            "SELECT b.id, b.type_besoin_id, t.nom AS type_nom, v.nom AS ville_nom, b.quantite, COALESCE(SUM(a.quantite_attribuee),0) AS recu, b.date_saisie
             FROM bngrc_besoin b
             LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
             LEFT JOIN bngrc_type_besoin t ON t.id = b.type_besoin_id
             LEFT JOIN bngrc_ville v ON v.id = b.ville_id
             GROUP BY b.id
             HAVING b.quantite > recu
             ORDER BY b.date_saisie ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        Flight::render('dispatch', ['summary' => $summary, 'leftDons' => $leftDons, 'leftBesoins' => $leftBesoins, 'error' => null]);
    }
}
