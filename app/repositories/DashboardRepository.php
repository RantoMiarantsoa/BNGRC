<?php

class DashboardRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getTableauParVille(): array
    {
        $sql = "SELECT v.id,
                       v.nom AS ville,
                       COALESCE(SUM(b.quantite), 0) AS total_besoin,
                       COALESCE(SUM(COALESCE(a.recu, 0)), 0) AS total_recu,
                       COALESCE(SUM(b.quantite - COALESCE(a.recu, 0)), 0) AS total_reste,
                       COALESCE(SUM((b.quantite - COALESCE(a.recu, 0)) * tb.prix_unitaire), 0) AS valeur_monetaire
                FROM bngrc_ville v
                LEFT JOIN bngrc_besoin b ON b.ville_id = v.id
                LEFT JOIN bngrc_type_besoin tb ON tb.id = b.type_besoin_id
                LEFT JOIN (
                    SELECT besoin_id, SUM(quantite_attribuee) AS recu
                    FROM bngrc_attribution
                    GROUP BY besoin_id
                ) a ON a.besoin_id = b.id
                GROUP BY v.id, v.nom
                ORDER BY v.nom ASC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function getIndicateursGlobaux(): array
    {
        $sql = "SELECT
                    b.total_besoins,
                    d.total_dons,
                    CASE
                        WHEN b.total_besoins = 0 THEN 0
                        ELSE ROUND(
                            d.total_dons * 100.0
                            / b.total_besoins, 2
                        )
                    END AS taux_couverture
                FROM
                    (SELECT COALESCE(SUM(quantite), 0) AS total_besoins FROM bngrc_besoin) AS b,
                    (SELECT COALESCE(SUM(quantite), 0) AS total_dons FROM bngrc_don) AS d";

        $stmt = $this->db->query($sql);

        return $stmt->fetch();
    }
}
