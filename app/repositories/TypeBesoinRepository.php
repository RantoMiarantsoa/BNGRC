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
            'SELECT id, nom, categorie, prix_unitaire
             FROM bngrc_type_besoin
             ORDER BY nom ASC'
        );

        return $stmt->fetchAll();
    }
}
