<?php

class BoardController {

    private DonRepository $donRepository;
    private BesoinRepository $besoinRepository;
    private AttributionRepository $attrRepository;
    private VilleRepository $villeRepository;

    public function __construct($db = null) {
        
        if ($db === null) {
            try {
                $db = Flight::db();
            } catch (\Throwable $e) {
                throw new \RuntimeException('Database connection is not available.', 0, $e);
            }
        }

        if ($db === null) {
            throw new \RuntimeException('Database connection is not available.');
        }
        
        $this->donRepository = new DonRepository($db);
        $this->besoinRepository = new BesoinRepository($db);
        $this->attrRepository = new AttributionRepository($db);
        $this->villeRepository = new VilleRepository($db); // VilleRepository accepte PDO
    }

    public function getBoard() {
        try {
            // === DONNÉES DES DONS ===
            $donNonDistribuer = $this->donRepository->getDonNonDistribuer();
            $argentDisponible = $this->donRepository->getArgentDisponible();
            $quantDonDispo = $this->donRepository->getQuantDonDispo();
            $donsParCategorie = $this->donRepository->getDisponiblesParCategorie();
            $donsParType = $this->donRepository->getDisponiblesParType();
            
            // === DONNÉES DES BESOINS ===
            $listeBesoins = $this->besoinRepository->getVillesAvecBesoinsPaType();
            $villesAffectees = $this->villeRepository->getVillesAvecBesoins();
            $totalBesoins = count($listeBesoins);
            
            // === DONNÉES DES DISTRIBUTIONS ===
            $attributionsReussies = $this->attrRepository->getTotalAttributions();
            $beneficiaires = $this->attrRepository->getBeneficiairesCount();
            $tauxReussite = $this->attrRepository->getTauxReussite();

            Flight::render('home', [
                // Données des Dons
                'totalDons' => $donNonDistribuer['total_dons_disponibles'] ?? 0,
                'argentDisponible' => $argentDisponible['argent_disponible'] ?? 0,
                'quantiteDons' => $quantDonDispo['quantite_dons_disponibles'] ?? 0,
                'donsParCategorie' => $donsParCategorie,
                'donsParType' => $donsParType,
                
                // Données des Besoins
                'villesAffectees' => count($villesAffectees),
                'totalBesoins' => $totalBesoins,
                'listeBesoins' => $listeBesoins,
                
                // Données des Distributions
                'attributionsReussies' => $attributionsReussies['total_attributions'] ?? 0,
                'beneficiaires' => $beneficiaires['total_beneficiaires'] ?? 0,
                'tauxReussite' => $tauxReussite['taux_reussite'] ?? 0,
                
                'title' => 'Tableau de Bord Complet - BNGRC'
            ]);

        } catch (Exception $e) {
            // Afficher l'erreur directement
            echo '<div style="padding:20px; color:red;">';
            echo '<h3>Erreur</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
    }
}