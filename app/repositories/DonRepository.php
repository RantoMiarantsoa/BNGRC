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
        $sql = 'SELECT t.id,
                       t.nom,
                       t.categorie,
                       t.prix_unitaire,
                       COALESCE(SUM(d.quantite), 0) AS quantite_totale
                FROM bngrc_type_besoin t
                LEFT JOIN bngrc_don d ON d.type_besoin_id = t.id
                GROUP BY t.id, t.nom, t.categorie, t.prix_unitaire
                ORDER BY t.nom ASC';

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }
}
