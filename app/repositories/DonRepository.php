<?php

class DonRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(string $nom, int $quantite, ?int $idTypeCategorie = null): int
    {
        $sql = 'INSERT INTO bngrc_don (id_type_categorie, nom, quantite, date_saisie)
                VALUES (?, ?, ?, CURRENT_TIMESTAMP)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $idTypeCategorie,
            $nom,
            $quantite
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getDisponiblesParType(): array
    {
        $sql = 'SELECT d.id,
                       d.nom,
                       c.nom AS categorie,
                       d.quantite,
                       d.date_saisie
                FROM bngrc_don d
                LEFT JOIN bngrc_categorie c ON d.id_type_categorie = c.id
                ORDER BY d.date_saisie DESC';

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }
}
