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
<<<<<<< Updated upstream
            'SELECT tb.id, tb.nom, c.nom as categorie, tb.prix_unitaire
             FROM bngrc_type_besoin tb
             LEFT JOIN bngrc_categorie c ON tb.categorie_id = c.id
=======
            'SELECT tb.id, tb.nom, c.nom AS categorie, tb.prix_unitaire
             FROM bngrc_type_besoin tb
             LEFT JOIN bngrc_categorie c ON c.id = tb.categorie_id
>>>>>>> Stashed changes
             ORDER BY tb.nom ASC'
        );

        return $stmt->fetchAll();
    }

    public function createOrGet(string $nom, int $categorie_id, ?float $prix_unitaire = null): int
    {
        // Chercher si le type existe déjà
        $stmt = $this->db->prepare(
            'SELECT id FROM bngrc_type_besoin 
             WHERE nom = ? AND categorie_id = ?'
        );
        $stmt->execute([$nom, $categorie_id]);
        $result = $stmt->fetch();
        
        if ($result) {
            return (int)$result['id'];
        }
        
        // Sinon le créer
        $stmt = $this->db->prepare(
            'INSERT INTO bngrc_type_besoin (nom, categorie_id, prix_unitaire) 
             VALUES (?, ?, ?)'
        );
        $stmt->execute([$nom, $categorie_id, $prix_unitaire]);
        
        return (int)$this->db->lastInsertId();
    }
}
