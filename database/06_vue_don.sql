CREATE OR REPLACE VIEW v_don_disp AS
SELECT 
    d.id,
    d.nom,
    d.quantite,
    d.date_saisie,
    d.id_type_categorie,
    COALESCE(SUM(a.quantite_attribuee), 0) AS attrib
FROM bngrc_don d
LEFT JOIN bngrc_attribution a 
       ON a.don_id = d.id
GROUP BY d.id
HAVING d.quantite > attrib
ORDER BY d.date_saisie ASC;
