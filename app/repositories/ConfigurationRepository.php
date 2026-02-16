<?php

class ConfigurationRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère une configuration par clé
     */
    public function obtenirParCle($cle) {
        $sql = "SELECT id, cle, valeur, description 
                FROM bngrc_configuration 
                WHERE cle = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cle]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les configurations
     */
    public function obtenirTous() {
        $sql = "SELECT id, cle, valeur, description 
                FROM bngrc_configuration";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour une configuration
     */
    public function mettreAJour($cle, $valeur) {
        $sql = "UPDATE bngrc_configuration 
                SET valeur = ? 
                WHERE cle = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$valeur, $cle]);
    }

    /**
     * Récupère le taux de frais d'achat
     */
    public function obtenirTauxFraisAchat() {
        $config = $this->obtenirParCle('taux_frais_achat');
        return $config ? (float) $config['valeur'] : 0;
    }
}
