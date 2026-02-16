<?php

class DonRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(int $typeBesoinId, int $quantite): int
    {
        $sql = 'INSERT INTO bngrc_don (type_besoin_id, quantite, date_saisie)
                VALUES (?, ?, CURRENT_TIMESTAMP)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $typeBesoinId,
            $quantite
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getDisponiblesParType(): array
    {
        $sql = 'SELECT * FROM vue_types_besoins_disponibles';

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un don par ID avec détails
     */
    public function obtenirParId($don_id) {
        $sql = "SELECT d.id, d.type_besoin_id, d.quantite, d.date_saisie,
                       t.categorie_id,
                       c.nom as categorie_nom
                FROM bngrc_don d
                LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
                LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
                WHERE d.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$don_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
}
