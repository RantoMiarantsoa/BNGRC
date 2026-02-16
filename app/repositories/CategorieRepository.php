<?php

class CategorieRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT id, nom FROM bngrc_categorie ORDER BY nom ASC'
        );

        return $stmt->fetchAll();
    }
}
