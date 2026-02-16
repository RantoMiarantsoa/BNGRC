<?php

class TypeBesoinRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT tb.id, tb.nom, c.nom AS categorie, tb.prix_unitaire
             FROM bngrc_type_besoin tb
             LEFT JOIN bngrc_categorie c ON c.id = tb.categorie_id
             ORDER BY tb.nom ASC'
        );

        return $stmt->fetchAll();
    }
}
