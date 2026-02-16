<?php

class BesoinRepository {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtenir toutes les villes avec le nombre total de besoins par type
     */
    public function getVillesAvecBesoins() {
        $query = "
            SELECT 
                c.id,
                c.name as city_name,
                c.affected_people,
                COUNT(b.id) as total_beneficiaries,
                SUM(b.needs) as total_needs
            FROM cities c
            LEFT JOIN beneficiaries b ON c.id = b.city_id
            GROUP BY c.id, c.name, c.affected_people
            ORDER BY total_needs DESC
        ";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les villes avec détail des besoins par type
     * Attention: à adapter selon votre structure de données réelle
     */
    public function getVillesAvecBesoinsPaType() {
        $query = "
            SELECT 
                c.id,
                c.name as city_name,
                c.affected_people,
                b.needs as need_type,
                COUNT(b.id) as total_items,
                SUM(b.needs) as total_quantity
            FROM cities c
            LEFT JOIN beneficiaries b ON c.id = b.city_id
            WHERE b.id IS NOT NULL
            GROUP BY c.id, c.name, c.affected_people, b.needs
            ORDER BY c.name ASC, b.needs ASC
        ";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir le résumé des besoins par ville (total simple)
     */
    public function getResumVilles() {
        $query = "
            SELECT 
                c.id,
                c.name as city_name,
                c.affected_people,
                COUNT(DISTINCT b.id) as beneficiaries_count,
                SUM(b.needs) as total_needs,
                AVG(b.needs) as avg_needs
            FROM cities c
            LEFT JOIN beneficiaries b ON c.id = b.city_id
            GROUP BY c.id, c.name, c.affected_people
            HAVING total_needs > 0
            ORDER BY total_needs DESC
        ";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les villes avec le pourcentage de satisfaction des besoins
     */
    public function obtenirTauxSatisfactionVilles() {
        $query = "
            SELECT 
                c.id,
                c.name as city_name,
                c.affected_people,
                COUNT(DISTINCT b.id) as total_beneficiaries,
                SUM(b.needs) as total_needs,
                COUNT(DISTINCT d.id) as distributed_count,
                SUM(d.quantity) as total_distributed,
                ROUND((SUM(d.quantity) / SUM(b.needs) * 100), 2) as satisfaction_rate
            FROM cities c
            LEFT JOIN beneficiaries b ON c.id = b.city_id
            LEFT JOIN distributions d ON b.id = d.beneficiary_id
            WHERE b.id IS NOT NULL
            GROUP BY c.id, c.name, c.affected_people
            ORDER BY satisfaction_rate DESC
        ";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les villes triées par besoins critiques
     */
    public function obtenirVillesParPriorite() {
        $query = "
            SELECT 
                c.id,
                c.name as city_name,
                c.affected_people,
                COUNT(DISTINCT b.id) as beneficiaries_count,
                SUM(b.needs) as total_needs,
                CASE 
                    WHEN SUM(b.needs) > 5000 THEN 'CRITIQUE'
                    WHEN SUM(b.needs) > 2000 THEN 'ÉLEVÉ'
                    WHEN SUM(b.needs) > 500 THEN 'MOYEN'
                    ELSE 'BAS'
                END as priority_level
            FROM cities c
            LEFT JOIN beneficiaries b ON c.id = b.city_id
            WHERE b.id IS NOT NULL
            GROUP BY c.id, c.name, c.affected_people
            ORDER BY total_needs DESC
        ";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
