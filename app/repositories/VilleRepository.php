<?php
    class VilleRepository{
            private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }


    public function getVille(){
        $query = "SELECT * FROM bngrc_ville";
         $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createVille($nom,$id_region){
        $query = "INSERT INTO bngrc_ville(nom,region_id) VALUES (?,?)";
                $stmt = $this->pdo->prepare($sql);
        
        $params = [
            $nom,
            $id_region
        ];
        
        $stmt->execute($params);
        return (int)$this->pdo->lastInsertId();
    }
    }