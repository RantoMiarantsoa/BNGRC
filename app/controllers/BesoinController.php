<?php
require_once __DIR__ . '/../repositories/BesoinRepository.php';
require_once __DIR__ . '/../repositories/VilleRepository.php';
require_once __DIR__ . '/../repositories/TypeBesoinRepository.php';

class BesoinController {
    private BesoinRepository $besoinRepository;
    private VilleRepository $villeRepository;
    private TypeBesoinRepository $typeBesoinRepository;

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
        $this->villeRepository = new VilleRepository($db);
        $this->typeBesoinRepository = new TypeBesoinRepository($db);
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

    public function showAjoutBesoin(): void {
        try {
            $villes = $this->villeRepository->getVille();
            
            Flight::render('besoins/besoin-ajout', [
                'villes' => $villes,
                'title' => 'Ajouter un Besoin'
            ]);
        } catch (Exception $e) {
            Flight::notFound();
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function storeBesoin(): void {
        try {
            $ville_id = (int) Flight::request()->data->ville_id;
            $categorie_id = (int) Flight::request()->data->categorie_id;
            $type_besoin_nom = Flight::request()->data->type_besoin_nom;
            $prix_unitaire = Flight::request()->data->prix_unitaire ?? null;
            $quantite = (int) Flight::request()->data->quantite;
            $dateSaisie = Flight::request()->data->date_saisie;
            $dateSaisie = (!empty($dateSaisie)) ? $dateSaisie . ' ' . date('H:i:s') : null;
            
            // Créer ou récupérer le type de besoin
            $type_besoin_id = $this->typeBesoinRepository->createOrGet(
                $type_besoin_nom,
                $categorie_id,
                $prix_unitaire ? (float)$prix_unitaire : null
            );
            
            // Créer le besoin
            $this->besoinRepository->create($ville_id, $type_besoin_id, $quantite, $dateSaisie);
            
            $listeBesoins = $this->besoinRepository->getVillesAvecBesoinsPaType();
            Flight::render('besoins/besoin-liste', [
                'besoins' => $listeBesoins,
                'title' => 'Liste des Besoins',
                'succes' => 'Besoin ajouté avec succès'
            ]);
        } catch (Exception $e) {
            Flight::notFound();
            echo 'Erreur: ' . $e->getMessage();
        }
    }
}