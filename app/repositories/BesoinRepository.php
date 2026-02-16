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

    public function create($ville_id,  $type_besoin_id, $quantite,  $date_saisie){
        $sql = "INSERT INTO bngrc_besoin (ville_id, type_besoin_id, quantite, date_saisie) 
                VALUES (?,?,?,?)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = [
            $ville_id,
            $type_besoin_id,
            $quantite,
            $date_saisie ?? date('Y-m-d H:i:s')
        ];
        
        $stmt->execute($params);
        
    }
}
