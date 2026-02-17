<?php

class DonRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(string $nom, int $quantite, ?int $idTypeCategorie = null, ?string $dateSaisie = null): int
    {
        $sql = 'INSERT INTO bngrc_don (id_type_categorie, nom, quantite, date_saisie)
                VALUES (?, ?, ?, ?)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $idTypeCategorie,
            $nom,
            $quantite,
            $dateSaisie ?? date('Y-m-d H:i:s')
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getDisponiblesParType(): array
    {

        $sql = 'SELECT * FROM vue_types_besoins_disponibles';

        $sql = 'SELECT d.id,
                       d.nom,
                       c.nom AS categorie,
                       d.quantite,
                       d.date_saisie
                FROM bngrc_don d
                LEFT JOIN bngrc_categorie c ON d.id_type_categorie = c.id
                ORDER BY d.date_saisie DESC';


        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un don par ID avec détails
     */
    public function obtenirParId($don_id) {
        $sql = "SELECT d.id, d.id_type_categorie as categorie_id, d.nom, d.quantite, d.date_saisie,
                       c.nom as categorie_nom
                FROM bngrc_don d
                LEFT JOIN bngrc_categorie c ON d.id_type_categorie = c.id
                WHERE d.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$don_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouveau don (pour finalisation d'achat)
     */
    public function creer($type_besoin_id, $quantite) {
        // Récupère le nom du type de besoin pour l'utiliser comme nom du don
        $sql = "SELECT nom FROM bngrc_type_besoin WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type_besoin_id]);
        $type = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$type) {
            return false;
        }
        
        // Récupère la catégorie du type de besoin
        $sql = "SELECT categorie_id FROM bngrc_type_besoin WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type_besoin_id]);
        $typeInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nom = 'Achat: ' . $type['nom'];
        
        $sql = 'INSERT INTO bngrc_don (id_type_categorie, nom, quantite, date_saisie)
                VALUES (?, ?, ?, CURRENT_TIMESTAMP)';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $typeInfo['categorie_id'],
            $nom,
            $quantite
        ]);
        
        return (int) $this->db->lastInsertId();
    }
    
}
