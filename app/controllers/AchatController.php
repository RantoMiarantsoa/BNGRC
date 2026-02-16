<?php

class AchatController {
    private $db;
    private $achatRepo;
    private $besoinRepo;
    private $donRepo;
    private $configRepo;

    public function __construct($db) {
        $this->db = $db;
        $this->achatRepo = new AchatRepository($db);
        $this->besoinRepo = new BesoinRepository($db);
        $this->donRepo = new DonRepository($db);
        $this->configRepo = new ConfigurationRepository($db);
    }

    /**
     * Affiche les besoins restants achètables
     */
    public function afficherBesoinsRestants() {
        try {
            $besoins = $this->achatRepo->obtenirBesoinsRestants();
            
            return Flight::view('achats/besoins-restants', ['besoins' => $besoins]);
        } catch (Exception $e) {
            Flight::json(['erreur' => $e->getMessage()], 500);
        }
    }

    /**
     * Affiche le formulaire de saisie d'achat pour un besoin
     */
    public function afficherSaisieAchat($besoin_id) {
        try {
            // Récupère le besoin
            $besoin = $this->besoinRepo->obtenirParId($besoin_id);
            
            if (!$besoin) {
                Flight::json(['erreur' => 'Besoin non trouvé'], 404);
                return;
            }
            
            // Récupère les dons en argent disponibles
            $donsArgent = $this->achatRepo->obtenirDonsArgentDisponibles();
            
            $tauxFrais = $this->configRepo->obtenirTauxFraisAchat();
            
            return Flight::view('achats/achat-saisie', [
                'besoin' => $besoin,
                'donsArgent' => $donsArgent,
                'tauxFrais' => $tauxFrais
            ]);
        } catch (Exception $e) {
            Flight::json(['erreur' => $e->getMessage()], 500);
        }
    }

    /**
     * Crée un nouvel achat
     */
    public function creerAchat() {
        try {
            $data = Flight::request()->data;
            $besoin_id = $data['besoin_id'] ?? null;
            $don_argent_id = $data['don_argent_id'] ?? null;
            $quantite = (int) ($data['quantite'] ?? 0);
            
            // Validation
            if (!$besoin_id || !$don_argent_id || $quantite <= 0) {
                Flight::json(['erreur' => 'Données invalides'], 400);
                return;
            }
            
            // Récupère le besoin
            $besoin = $this->besoinRepo->obtenirParId($besoin_id);
            if (!$besoin) {
                Flight::json(['erreur' => 'Besoin non trouvé'], 404);
                return;
            }
            
            // Récupère le don argent
            $donArgent = $this->donRepo->obtenirParId($don_argent_id);
            if (!$donArgent) {
                Flight::json(['erreur' => 'Don argent non trouvé'], 404);
                return;
            }
            
            // Vérifier que c'est un don ARGENTS (categorie_id = 3)
            if ($donArgent['categorie_id'] != 3) {
                Flight::json(['erreur' => 'Don invalide - doit être ARGENTS'], 400);
                return;
            }
            
            // Vérifier qu'il n'y a pas d'achat en cours sur ce don
            if ($this->achatRepo->donAchatEnCours($don_argent_id)) {
                Flight::json(['erreur' => 'Un achat est déjà en cours sur ce don'], 400);
                return;
            }
            
            // Récupère le prix unitaire du type acheté
            $cout_unitaire = (float) $besoin['prix_unitaire'];
            if (!$cout_unitaire) {
                Flight::json(['erreur' => 'Type de besoin sans prix unitaire'], 400);
                return;
            }
            
            // Calcul des montants
            $taux_frais = $this->configRepo->obtenirTauxFraisAchat();
            $montant_achat = $quantite * $cout_unitaire;
            $frais_achat = round($montant_achat * ($taux_frais / 100), 2);
            $montant_total = $montant_achat + $frais_achat;
            
            // Vérifier la disponibilité du don
            $dispo = (float) $donArgent['quantite'] - $this->achatRepo->montantTotalDonUtilise($don_argent_id);
            if ($dispo < $montant_total) {
                Flight::json([
                    'erreur' => 'Montant insuffisant',
                    'demandé' => $montant_total,
                    'disponible' => $dispo
                ], 400);
                return;
            }
            
        
            $achat_id = $this->achatRepo->creer(
                $don_argent_id,
                $besoin_id,
                $quantite,
                $cout_unitaire,
                $taux_frais,
                $montant_achat,
                $frais_achat,
                $montant_total
            );
            
            if ($achat_id) {
                Flight::json([
                    'succès' => true,
                    'achat_id' => $achat_id,
                    'message' => 'Achat créé en attente de finalisation'
                ]);
            } else {
                Flight::json(['erreur' => 'Erreur création achat'], 500);
            }
        } catch (Exception $e) {
            Flight::json(['erreur' => $e->getMessage()], 500);
        }
    }

    /**
     * Affiche la liste des achats
     */
    public function afficherListeAchats() {
        try {
            $statut = Flight::request()->query['statut'] ?? null;
            $ville_id = Flight::request()->query['ville_id'] ?? null;
            
            $achats = $this->achatRepo->obtenirTous($statut, $ville_id);
            
            // Récupère les villes pour le filtre
            $stmt = $this->db->prepare("SELECT id, nom FROM bngrc_ville ORDER BY nom");
            $stmt->execute();
            $villes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return Flight::view('achats/achat-liste', [
                'achats' => $achats,
                'villes' => $villes,
                'statut_filtre' => $statut,
                'ville_filtre' => $ville_id
            ]);
        } catch (Exception $e) {
            Flight::json(['erreur' => $e->getMessage()], 500);
        }
    }

    /**
     * Finalise un achat (crée l'attribution)
     */
    public function finaliserAchat($achat_id) {
        try {
            $achat = $this->achatRepo->obtenirParId($achat_id);
            
            if (!$achat) {
                Flight::json(['erreur' => 'Achat non trouvé'], 404);
                return;
            }
            
            if ($achat['statut'] !== 'en_cours') {
                Flight::json(['erreur' => 'Achat pas en cours'], 400);
                return;
            }
            
            // Crée un don du type acheté
            $don_id = $this->donRepo->creer($achat['besoin_type_id'], $achat['quantite']);
            
            if (!$don_id) {
                Flight::json(['erreur' => 'Erreur création don'], 500);
                return;
            }
            
            // Crée l'attribution
            $attributionRepo = new AttributionRepository($this->db);
            $attribution_id = $attributionRepo->creer($don_id, $achat['besoin_id'], $achat['quantite']);
            
            if ($attribution_id) {
                // Marque comme finalisé
                $this->achatRepo->mettreAJourStatut($achat_id, 'finalisé');
                
                Flight::json(['succès' => true, 'message' => 'Achat finalisé']);
            } else {
                Flight::json(['erreur' => 'Erreur attribution'], 500);
            }
        } catch (Exception $e) {
            Flight::json(['erreur' => $e->getMessage()], 500);
        }
    }

    /**
     * Annule un achat
     */
    public function annulerAchat($achat_id) {
        try {
            $achat = $this->achatRepo->obtenirParId($achat_id);
            
            if (!$achat) {
                Flight::json(['erreur' => 'Achat non trouvé'], 404);
                return;
            }
            
            if ($achat['statut'] !== 'en_cours') {
                Flight::json(['erreur' => 'Seuls les achats en cours peuvent être annulés'], 400);
                return;
            }
            
            if ($this->achatRepo->annuler($achat_id)) {
                Flight::json(['succès' => true, 'message' => 'Achat annulé']);
            } else {
                Flight::json(['erreur' => 'Erreur annulation'], 500);
            }
        } catch (Exception $e) {
            Flight::json(['erreur' => $e->getMessage()], 500);
        }
    }
}
