<?php

class BesoinRepository {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

   
    public function getVillesAvecBesoinsPaType() {
        $query = "SELECT * FROM vue_besoins_par_type";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}


