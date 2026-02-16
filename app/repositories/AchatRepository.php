<?php

class AchatRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Crée un nouvel achat
     */
    public function creer($don_argent_id, $besoin_id, $quantite, $cout_unitaire, $taux_frais, $montant_achat, $frais_achat, $montant_total) {
        $sql = "INSERT INTO bngrc_achat 
                (don_argent_id, besoin_id, quantite, cout_unitaire, taux_frais, montant_achat, frais_achat, montant_total, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en_cours')";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute([$don_argent_id, $besoin_id, $quantite, $cout_unitaire, $taux_frais, $montant_achat, $frais_achat, $montant_total])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Récupère un achat par ID avec détails
     */
    public function obtenirParId($id) {
        $sql = "SELECT a.*, 
                       b.quantite as besoin_quantite,
                       tb.nom as type_nom,
                       tb.id as type_besoin_id,
                       c.nom as categorie_nom,
                       v.nom as ville_nom,
                       d.nom as don_nom
                FROM bngrc_achat a
                JOIN bngrc_besoin b ON a.besoin_id = b.id
                JOIN bngrc_type_besoin tb ON b.type_besoin_id = tb.id
                JOIN bngrc_categorie c ON tb.categorie_id = c.id
                JOIN bngrc_ville v ON b.ville_id = v.id
                JOIN bngrc_don d ON a.don_argent_id = d.id
                WHERE a.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les achats avec filtrage optionnel par statut ou ville
     */
    public function obtenirTous($statut = null, $ville_id = null) {
        $sql = "SELECT * FROM vue_achats_par_ville WHERE 1=1";
        
        $params = [];
        
        if ($statut) {
            $sql .= " AND statut = ?";
            $params[] = $statut;
        }
        
        if ($ville_id) {
            $sql .= " AND ville_id = ?";
            $params[] = $ville_id;
        }
        
        $sql .= " ORDER BY date_achat DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les achats en cours avec détails
     */
    public function obtenirEnCours($ville_id = null) {
        $sql = "SELECT a.*, 
                       b.quantite as besoin_quantite,
                       tb.nom as type_nom,
                       tb.id as type_besoin_id,
                       c.nom as categorie_nom,
                       v.nom as ville_nom,
                       d.nom as don_nom
                FROM bngrc_achat a
                JOIN bngrc_besoin b ON a.besoin_id = b.id
                JOIN bngrc_type_besoin tb ON b.type_besoin_id = tb.id
                JOIN bngrc_categorie c ON tb.categorie_id = c.id
                JOIN bngrc_ville v ON b.ville_id = v.id
                JOIN bngrc_don d ON a.don_argent_id = d.id
                WHERE a.statut = 'en_cours'";
        
        $params = [];
        
        if ($ville_id) {
            $sql .= " AND v.id = ?";
            $params[] = $ville_id;
        }
        
        $sql .= " ORDER BY a.date_achat DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les besoins restants achètables
     */
    public function obtenirBesoinsRestants() {
        $sql = "SELECT * FROM vue_besoins_achatable ORDER BY ville_nom, categorie_nom, type_nom";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les dons argent disponibles
     */
    public function obtenirDonsArgentDisponibles() {
        $sql = "SELECT d.id, d.nom, d.quantite, 
                       COALESCE(SUM(a.montant_total), 0) as utilisé,
                       (d.quantite - COALESCE(SUM(a.montant_total), 0)) as disponible
                FROM bngrc_don d
                LEFT JOIN bngrc_achat a ON a.don_argent_id = d.id 
                   AND a.statut IN ('en_cours', 'finalisé')
                WHERE d.id_type_categorie = 3
                GROUP BY d.id
                HAVING disponible > 0
                ORDER BY d.date_saisie DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour le statut d'un achat
     */
    public function mettreAJourStatut($id, $statut) {
        $sql = "UPDATE bngrc_achat 
                SET statut = ? 
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$statut, $id]);
    }

    /**
     * Vérifie si un don argent a un achat en cours
     */
    public function donAchatEnCours($don_argent_id) {
        $sql = "SELECT COUNT(*) as count 
                FROM bngrc_achat 
                WHERE don_argent_id = ? AND statut = 'en_cours'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$don_argent_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Récupère le montant total dépensé d'un don argent
     */
    public function montantTotalDonUtilise($don_argent_id) {
        $sql = "SELECT COALESCE(SUM(montant_total), 0) as total 
                FROM bngrc_achat 
                WHERE don_argent_id = ? AND statut IN ('en_cours', 'finalisé')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$don_argent_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) $result['total'];
    }

    /**
     * Supprime un achat (annulation)
     */
    public function annuler($id) {
        $sql = "UPDATE bngrc_achat 
                SET statut = 'annulé' 
                WHERE id = ? AND statut = 'en_cours'";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
