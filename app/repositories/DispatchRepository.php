<?php

class DispatchRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Récupère toutes les attributions avec détails
     */
    public function obtenirAttributions(): array {
        return $this->db->query(
            "SELECT a.id, a.quantite_attribuee, a.date_dispatch,
                    d.nom AS don_nom, d.quantite AS don_quantite,
                    b.quantite AS besoin_quantite, b.date_saisie AS besoin_date,
                    c.nom AS type_nom, v.nom AS ville_nom
             FROM bngrc_attribution a
             JOIN bngrc_don d ON d.id = a.don_id
             JOIN bngrc_besoin b ON b.id = a.besoin_id
             JOIN bngrc_type_besoin t ON t.id = b.type_besoin_id
             JOIN bngrc_categorie c ON c.id = t.categorie_id
             JOIN bngrc_ville v ON v.id = b.ville_id
             ORDER BY a.date_dispatch DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Réinitialise toutes les attributions
     */
    public function resetAttributions(): void {
        $this->db->exec('TRUNCATE TABLE bngrc_attribution');
    }

    /**
     * Récupère toutes les catégories
     */
    public function obtenirCategories(): array {
        return $this->db->query('SELECT id, nom FROM bngrc_categorie')->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les besoins non satisfaits pour une catégorie donnée
     * @param string $strategy 'oldest' (FIFO par date) ou 'smallest' (plus petits besoins d'abord)
     */
    public function obtenirBesoinsNonSatisfaitsParCategorie(int $categorieId, string $strategy = 'oldest'): array {
        $orderBy = ($strategy === 'smallest') 
            ? 'ORDER BY restant ASC, b.date_saisie ASC, b.ordre ASC'
            : 'ORDER BY b.date_saisie ASC, b.ordre ASC';

        $stmt = $this->db->prepare(
            "SELECT b.id, b.quantite, b.date_saisie, b.ordre, b.ville_id, v.nom AS ville_nom, t.nom AS type_nom, 
                    COALESCE(SUM(a.quantite_attribuee),0) AS recu,
                    (b.quantite - COALESCE(SUM(a.quantite_attribuee),0)) AS restant
             FROM bngrc_besoin b
             LEFT JOIN bngrc_ville v ON v.id = b.ville_id
             LEFT JOIN bngrc_type_besoin t ON t.id = b.type_besoin_id
             LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
             WHERE t.categorie_id = ?
             GROUP BY b.id
             HAVING restant > 0
             {$orderBy}"
        );
        $stmt->execute([$categorieId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les dons disponibles correspondant à un nom de type de besoin (FIFO)
     * On fait la correspondance par nom : don.nom doit correspondre au type_besoin.nom
     */
    public function obtenirDonsDisponiblesParNomType(string $nomType): array {
        $stmt = $this->db->prepare(

            "SELECT d.id, d.nom, d.quantite, d.date_saisie, COALESCE(SUM(a.quantite_attribuee),0) AS attrib
             FROM bngrc_don d
             LEFT JOIN bngrc_attribution a ON a.don_id = d.id
             WHERE LOWER(TRIM(d.nom)) = LOWER(TRIM(?))
             GROUP BY d.id
             HAVING d.quantite > attrib
             ORDER BY d.date_saisie ASC"
        );
        $stmt->execute([$nomType]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les types de besoins avec leur catégorie
     */
    public function obtenirTypesBesoins(): array {
        return $this->db->query(
            'SELECT t.id, t.nom, t.categorie_id, c.nom AS categorie_nom 
             FROM bngrc_type_besoin t 
             JOIN bngrc_categorie c ON c.id = t.categorie_id'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les besoins non satisfaits pour un type de besoin spécifique
     * @param string $strategy 'oldest' (FIFO par date) ou 'smallest' (plus petits besoins d'abord)
     */
    public function obtenirBesoinsNonSatisfaitsParType(int $typeId, string $strategy = 'oldest'): array {
        $orderBy = ($strategy === 'smallest') 
            ? 'ORDER BY restant ASC, b.date_saisie ASC, b.ordre ASC'
            : 'ORDER BY b.date_saisie ASC, b.ordre ASC';

        $stmt = $this->db->prepare(
            "SELECT b.id, b.quantite, b.date_saisie, b.ordre, b.ville_id, v.nom AS ville_nom, t.nom AS type_nom, 
                    COALESCE(SUM(a.quantite_attribuee),0) AS recu,
                    (b.quantite - COALESCE(SUM(a.quantite_attribuee),0)) AS restant
             FROM bngrc_besoin b
             LEFT JOIN bngrc_ville v ON v.id = b.ville_id
             LEFT JOIN bngrc_type_besoin t ON t.id = b.type_besoin_id
             LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
             WHERE b.type_besoin_id = ?
             GROUP BY b.id
             HAVING restant > 0
             {$orderBy}"
        );

        $stmt->execute([$typeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function creerAttribution(int $donId, int $besoinId, int $quantite): bool {
        $stmt = $this->db->prepare('INSERT INTO bngrc_attribution (don_id, besoin_id, quantite_attribuee) VALUES (?, ?, ?)');
        return $stmt->execute([$donId, $besoinId, $quantite]);
    }

    
    public function obtenirDonsRestants(): array {
        return $this->db->query(
            "SELECT d.id, d.nom, d.quantite, COALESCE(SUM(a.quantite_attribuee),0) AS attrib, d.date_saisie
             FROM bngrc_don d
             LEFT JOIN bngrc_attribution a ON a.don_id = d.id
             GROUP BY d.id
             HAVING d.quantite > attrib
             ORDER BY d.date_saisie ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les dons avec leurs attributions actuelles
     */
    public function obtenirTousLesDons(): array {
        return $this->db->query(
            "SELECT d.id, d.nom, d.quantite, COALESCE(SUM(a.quantite_attribuee),0) AS attrib, d.date_saisie
             FROM bngrc_don d
             LEFT JOIN bngrc_attribution a ON a.don_id = d.id
             GROUP BY d.id
             ORDER BY d.date_saisie ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les besoins restants (non totalement satisfaits)
     */
    public function obtenirBesoinsRestants(): array {
        return $this->db->query(
            "SELECT b.id, b.type_besoin_id, t.nom AS type_nom, t.prix_unitaire, t.categorie_id, c.nom AS categorie_nom, v.nom AS ville_nom, b.quantite, COALESCE(SUM(a.quantite_attribuee),0) AS recu, b.date_saisie
             FROM bngrc_besoin b
             LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
             LEFT JOIN bngrc_type_besoin t ON t.id = b.type_besoin_id
             LEFT JOIN bngrc_categorie c ON c.id = t.categorie_id
             LEFT JOIN bngrc_ville v ON v.id = b.ville_id
             GROUP BY b.id
             HAVING b.quantite > recu
             ORDER BY v.nom ASC, c.nom ASC, t.nom ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Démarre une transaction
     */
    public function beginTransaction(): void {
        $this->db->beginTransaction();
    }

    /**
     * Valide une transaction
     */
    public function commit(): void {
        $this->db->commit();
    }

    /**
     * Annule une transaction
     */
    public function rollBack(): void {
        $this->db->rollBack();
    }
}
