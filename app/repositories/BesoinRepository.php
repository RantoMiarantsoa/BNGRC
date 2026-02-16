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

    /**
     * Récupère un besoin par ID avec détails
     */
    public function obtenirParId($besoin_id) {
        $sql = "SELECT b.*, t.nom as type_nom, t.prix_unitaire, c.id as categorie_id, c.nom as categorie_nom, v.nom as ville_nom
                FROM bngrc_besoin b
                LEFT JOIN bngrc_type_besoin t ON b.type_besoin_id = t.id
                LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
                LEFT JOIN bngrc_ville v ON b.ville_id = v.id
                WHERE b.id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$besoin_id]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);


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
