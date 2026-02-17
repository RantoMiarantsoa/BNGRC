<?php

class AttributionRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Crée une nouvelle attribution
     */
    public function creer($don_id, $besoin_id, $quantite_attribuee) {
        $sql = "INSERT INTO bngrc_attribution 
                (don_id, besoin_id, quantite_attribuee) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute([$don_id, $besoin_id, $quantite_attribuee])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Récupère une attribution par ID
     */
    public function obtenirParId($id) {
        $sql = "SELECT a.*, d.quantite as don_quantite, b.quantite as besoin_quantite,
                       t.nom as type_nom, v.nom as ville_nom
                FROM bngrc_attribution a
                LEFT JOIN bngrc_don d ON a.don_id = d.id
                LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
                LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
                LEFT JOIN bngrc_ville v ON b.ville_id = v.id
                WHERE a.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les attributions
     */
    public function obtenirTous() {
        $sql = "SELECT a.id, a.don_id, a.besoin_id, a.quantite_attribuee, a.date_dispatch,
                       d.quantite as don_quantite, b.quantite as besoin_quantite,
                       t.nom as type_nom, v.nom as ville_nom
                FROM bngrc_attribution a
                LEFT JOIN bngrc_don d ON a.don_id = d.id
                LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
                LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
                LEFT JOIN bngrc_ville v ON b.ville_id = v.id
                ORDER BY a.date_dispatch DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les attributions par besoin
     */
    public function obtenirParBesoin($besoin_id) {
        $sql = "SELECT a.*, d.quantite as don_quantite, 
                       t.nom as type_nom
                FROM bngrc_attribution a
                LEFT JOIN bngrc_don d ON a.don_id = d.id
                LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
                WHERE a.besoin_id = ?
                ORDER BY a.date_dispatch DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$besoin_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les attributions par don
     */
    public function obtenirParDon($don_id) {
        $sql = "SELECT a.*, b.quantite as besoin_quantite, 
                       v.nom as ville_nom
                FROM bngrc_attribution a
                LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
                LEFT JOIN bngrc_ville v ON b.ville_id = v.id
                WHERE a.don_id = ?
                ORDER BY a.date_dispatch DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$don_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une attribution
     */
    public function supprimer($id) {
        $sql = "DELETE FROM bngrc_attribution WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Récupère le total des attributions réussies
     */
    public function getTotalAttributions() {
        $sql = "SELECT COUNT(*) AS total_attributions FROM bngrc_attribution";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le nombre total de bénéficiaires
     */
    public function getBeneficiairesCount() {
        $sql = "SELECT COUNT(DISTINCT b.ville_id) AS total_beneficiaires 
                FROM bngrc_attribution a 
                INNER JOIN bngrc_besoin b ON a.besoin_id = b.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le taux de réussite des attributions
     */
    public function getTauxReussite() {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM bngrc_attribution) AS total_attributions,
                    (SELECT COUNT(*) FROM bngrc_besoin b 
                     WHERE (SELECT COALESCE(SUM(quantite_attribuee), 0) FROM bngrc_attribution a WHERE a.besoin_id = b.id) >= b.quantite) AS besoins_satisfaits";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $taux = ($result['total_attributions'] > 0) ? ($result['besoins_satisfaits'] / $result['total_attributions'] * 100) : 0;
        
        return ['taux_reussite' => round($taux, 1)];
    }
}