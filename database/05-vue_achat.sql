-- ============================
-- VUES POUR ACHATS ET ATTRIBUTIONS
-- ============================
-- DROP VIEW IF EXISTS vue_achat_detail, vue_achats_complet, vue_achats_par_ville, 
--                    vue_achats_en_cours, vue_attributions_complet, vue_besoins_achatable, 
--                    vue_types_besoins_disponibles, vue_achats_resume_ville, vue_dons_disponibles;

-- ============================
-- VUE: Détails d'un achat
-- ============================
CREATE OR REPLACE VIEW vue_achat_detail AS
SELECT a.id, 
       a.don_argent_id, 
       a.besoin_id, 
       a.quantite, 
       a.cout_unitaire, 
       a.taux_frais, 
       a.montant_achat, 
       a.frais_achat, 
       a.montant_total, 
       a.statut, 
       a.date_achat,
       -- Info du don argent
       d.quantite as don_quantite,
       d.id_type_categorie as don_categorie_id,
       d.nom as don_nom,
       cd.nom as don_categorie_nom,
       -- Info du besoin
       b.quantite as besoin_quantite,
       b.type_besoin_id,
       tb.nom as besoin_type_nom,
       tb.prix_unitaire,
       cb.nom as besoin_categorie_nom,
       v.id as ville_id,
       v.nom as ville_nom
FROM bngrc_achat a
LEFT JOIN bngrc_don d ON a.don_argent_id = d.id
LEFT JOIN bngrc_categorie cd ON d.id_type_categorie = cd.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_type_besoin tb ON b.type_besoin_id = tb.id
LEFT JOIN bngrc_categorie cb ON tb.categorie_id = cb.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id;

-- ============================
-- VUE: Tous les achats avec détails
-- ============================
CREATE OR REPLACE VIEW vue_achats_complet AS
SELECT a.id, 
       a.don_argent_id, 
       a.besoin_id, 
       a.quantite, 
       a.cout_unitaire, 
       a.taux_frais, 
       a.montant_achat, 
       a.frais_achat, 
       a.montant_total, 
       a.statut, 
       a.date_achat,
       d.quantite as don_quantite,
       d.nom as don_nom,
       cd.nom as don_categorie_nom,
       tb.nom as besoin_type_nom,
       cb.nom as besoin_categorie_nom,
       b.quantite as besoin_quantite,
       v.nom as ville_nom
FROM bngrc_achat a
LEFT JOIN bngrc_don d ON a.don_argent_id = d.id
LEFT JOIN bngrc_categorie cd ON d.id_type_categorie = cd.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_type_besoin tb ON b.type_besoin_id = tb.id
LEFT JOIN bngrc_categorie cb ON tb.categorie_id = cb.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id
ORDER BY a.date_achat DESC;

-- ============================
-- VUE: Achats par ville et statut
-- ============================
CREATE OR REPLACE VIEW vue_achats_par_ville AS
SELECT a.id, 
       a.don_argent_id, 
       a.besoin_id, 
       a.quantite, 
       a.cout_unitaire, 
       a.taux_frais, 
       a.montant_achat, 
       a.frais_achat, 
       a.montant_total, 
       a.statut, 
       a.date_achat,
       v.id as ville_id,
       v.nom as ville_nom,
       d.nom as don_nom,
       cd.nom as don_categorie_nom,
       tb.nom as besoin_type_nom,
       b.quantite as besoin_quantite
FROM bngrc_achat a
LEFT JOIN bngrc_don d ON a.don_argent_id = d.id
LEFT JOIN bngrc_categorie cd ON d.id_type_categorie = cd.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_type_besoin tb ON b.type_besoin_id = tb.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id;

-- ============================
-- VUE: Achats en cours uniquement
-- ============================
CREATE OR REPLACE VIEW vue_achats_en_cours AS
SELECT a.id, 
       a.don_argent_id, 
       a.besoin_id, 
       a.quantite, 
       a.cout_unitaire, 
       a.taux_frais, 
       a.montant_achat, 
       a.frais_achat, 
       a.montant_total, 
       a.date_achat,
       d.nom as don_nom,
       cd.nom as don_categorie_nom,
       tb.nom as besoin_type_nom,
       v.nom as ville_nom
FROM bngrc_achat a
LEFT JOIN bngrc_don d ON a.don_argent_id = d.id
LEFT JOIN bngrc_categorie cd ON d.id_type_categorie = cd.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_type_besoin tb ON b.type_besoin_id = tb.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id
WHERE a.statut = 'en_cours'
ORDER BY a.date_achat DESC;

-- ============================
-- VUE: Attributions avec détails complets
-- ============================
CREATE OR REPLACE VIEW vue_attributions_complet AS
SELECT a.id, 
       a.don_id, 
       a.besoin_id, 
       a.quantite_attribuee, 
       a.date_dispatch,
       d.quantite as don_quantite,
       d.id_type_categorie as don_categorie_id,
       d.nom as don_nom,
       b.quantite as besoin_quantite,
       b.type_besoin_id,
       cd.nom as don_categorie_nom,
       tb.nom as besoin_type_nom,
       cb.nom as besoin_categorie_nom,
       v.nom as ville_nom
FROM bngrc_attribution a
LEFT JOIN bngrc_don d ON a.don_id = d.id
LEFT JOIN bngrc_categorie cd ON d.id_type_categorie = cd.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_type_besoin tb ON b.type_besoin_id = tb.id
LEFT JOIN bngrc_categorie cb ON tb.categorie_id = cb.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id
ORDER BY a.date_dispatch DESC;

-- ============================
-- VUE: Résumé des achats par ville
-- ============================
CREATE OR REPLACE VIEW vue_achats_resume_ville AS
SELECT v.id as ville_id,
       v.nom as ville_nom,
       COUNT(a.id) as nombre_achats,
       COUNT(CASE WHEN a.statut = 'en_cours' THEN 1 END) as achats_en_cours,
       COUNT(CASE WHEN a.statut = 'finalisé' THEN 1 END) as achats_finalisés,
       COUNT(CASE WHEN a.statut = 'annulé' THEN 1 END) as achats_annulés,
       SUM(CASE WHEN a.statut IN ('en_cours', 'finalisé') THEN a.montant_total ELSE 0 END) as montant_total_investis
FROM bngrc_ville v
LEFT JOIN bngrc_besoin b ON v.id = b.ville_id
LEFT JOIN bngrc_achat a ON b.id = a.besoin_id
GROUP BY v.id, v.nom
ORDER BY v.nom;

-- ============================
-- VUE: Besoins restants achètables (nature et matériels)
-- ============================
CREATE OR REPLACE VIEW vue_besoins_achatable AS
SELECT b.id, 
       b.quantite, 
       b.date_saisie,
       b.type_besoin_id,
       b.ville_id,
       t.nom as type_nom, 
       t.prix_unitaire,
       t.categorie_id,
       c.nom as categorie_nom,
       v.nom as ville_nom,
       COALESCE(SUM(a.quantite_attribuee), 0) as quantite_attribuee,
       (b.quantite - COALESCE(SUM(a.quantite_attribuee), 0)) as reste
FROM bngrc_besoin b
LEFT JOIN bngrc_type_besoin t ON b.type_besoin_id = t.id
LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id
LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
WHERE c.id IN (1, 2) AND t.prix_unitaire IS NOT NULL
GROUP BY b.id, t.id, v.id
HAVING reste > 0
ORDER BY v.nom, c.nom, t.nom;

-- ============================
-- VUE: Types de besoins avec quantités disponibles
-- ============================
CREATE OR REPLACE VIEW vue_types_besoins_disponibles AS
SELECT t.id,
       t.nom,
       c.nom as categorie,
       c.id as categorie_id,
       t.prix_unitaire,
       COALESCE(SUM(b.quantite), 0) - COALESCE(SUM(a.quantite_attribuee), 0) AS quantite_disponible
FROM bngrc_type_besoin t
LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
LEFT JOIN bngrc_besoin b ON b.type_besoin_id = t.id
LEFT JOIN bngrc_attribution a ON a.besoin_id = b.id
GROUP BY t.id, t.nom, c.id, c.nom, t.prix_unitaire
ORDER BY c.nom, t.nom ASC;

-- ============================
-- VUE: Dons disponibles par catégorie
-- ============================
CREATE OR REPLACE VIEW vue_dons_disponibles AS
SELECT d.id,
       d.id_type_categorie,
       d.nom,
       d.quantite,
       d.date_saisie,
       c.nom as categorie_nom,
       COALESCE(SUM(a.quantite_attribuee), 0) as quantite_attribuee,
       (d.quantite - COALESCE(SUM(a.quantite_attribuee), 0)) as quantite_disponible
FROM bngrc_don d
LEFT JOIN bngrc_categorie c ON d.id_type_categorie = c.id
LEFT JOIN bngrc_attribution a ON a.don_id = d.id
GROUP BY d.id, c.id
HAVING quantite_disponible > 0
ORDER BY c.nom, d.date_saisie DESC;

