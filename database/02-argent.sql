-- ============================================
-- Gestion spéciale pour la catégorie ARGENTS
-- ============================================

-- Mettre à jour les types ARGENTS: prix unitaire = NULL
UPDATE bngrc_type_besoin 
SET prix_unitaire = NULL 
WHERE categorie_id = 3;

-- Recréer la vue pour gérer ARGENTS différemment
DROP VIEW IF EXISTS vue_besoins_par_type;

CREATE VIEW vue_besoins_par_type AS
SELECT 
    v.id as ville_id,
    v.nom as nom_ville,
    r.nom as nom_region,
    tb.id as type_besoin_id,
    tb.nom as type_besoin,
    cat.id as categorie_id,
    cat.nom as categorie,
    tb.prix_unitaire,
    COUNT(DISTINCT b.id) as nombre_besoins,
    SUM(b.quantite) as quantite_totale,
    -- Pour ARGENTS: montant direct | Pour autres: quantité × prix
    CASE 
        WHEN cat.nom = 'ARGENTS' THEN SUM(b.quantite)
        ELSE SUM(b.quantite * COALESCE(tb.prix_unitaire, 0))
    END as valeur_totale,
    b.date_saisie as date_derniere_saisie,
    cat.nom as type_affichage
FROM bngrc_ville v
LEFT JOIN bngrc_region r ON v.region_id = r.id
LEFT JOIN bngrc_besoin b ON v.id = b.ville_id
LEFT JOIN bngrc_type_besoin tb ON b.type_besoin_id = tb.id
LEFT JOIN bngrc_categorie cat ON tb.categorie_id = cat.id
WHERE b.id IS NOT NULL
GROUP BY 
    v.id, v.nom, r.nom, 
    tb.id, tb.nom, cat.id, cat.nom, 
    tb.prix_unitaire, b.date_saisie
ORDER BY v.nom ASC, cat.nom ASC, tb.nom ASC;
