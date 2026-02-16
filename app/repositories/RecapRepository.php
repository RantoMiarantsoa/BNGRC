<?php

class RecapRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getRecap(): array
    {
        $sql = "SELECT
                    COALESCE(SUM(b.quantite * tb.prix_unitaire), 0) AS montant_besoins_totaux,
                    COALESCE(SUM(COALESCE(a.recu, 0) * tb.prix_unitaire), 0) AS montant_besoins_satisfaits,
                    COALESCE(SUM((b.quantite - COALESCE(a.recu, 0)) * tb.prix_unitaire), 0) AS montant_besoins_restants
                FROM bngrc_besoin b
                JOIN bngrc_type_besoin tb ON tb.id = b.type_besoin_id
                LEFT JOIN (
                    SELECT besoin_id, SUM(quantite_attribuee) AS recu
                    FROM bngrc_attribution
                    GROUP BY besoin_id
                ) a ON a.besoin_id = b.id";

        $stmt = $this->db->query($sql);

        return $stmt->fetch();
    }
}
