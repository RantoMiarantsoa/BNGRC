-- ============================
-- VUES POUR ACHATS ET ATTRIBUTIONS
-- ============================

-- ============================
-- VUE: Détails d'un achat
-- ============================
CREATE OR REPLACE VIEW vue_achat_detail AS
SELECT a.id, a.don_argent_id, a.besoin_id, a.quantite, a.cout_unitaire, 
       a.taux_frais, a.montant_achat, a.frais_achat, a.montant_total, 
       a.statut, a.date_achat,
       d.quantite as don_quantite, 
       t.nom as type_nom, 
       b.type_besoin_id as besoin_type_id,
       b.quantite as besoin_quantite, 
       v.nom as ville_nom
FROM bngrc_achat a
LEFT JOIN bngrc_don d ON a.don_argent_id = d.id
LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id;

-- ============================
-- VUE: Tous les achats avec détails
-- ============================
CREATE OR REPLACE VIEW vue_achats_complet AS
SELECT a.id, a.don_argent_id, a.besoin_id, a.quantite, a.cout_unitaire, 
       a.taux_frais, a.montant_achat, a.frais_achat, a.montant_total, 
       a.statut, a.date_achat,
       t.nom as type_nom, 
       b.quantite as besoin_quantite, 
       v.nom as ville_nom, 
       c.nom as categorie_nom,
       d.quantite as don_quantite
FROM bngrc_achat a
LEFT JOIN bngrc_don d ON a.don_argent_id = d.id
LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id
ORDER BY a.date_achat DESC;

-- ============================
-- VUE: Achats par ville et statut
-- ============================
CREATE OR REPLACE VIEW vue_achats_par_ville AS
SELECT a.id, a.don_argent_id, a.besoin_id, a.quantite, a.cout_unitaire, 
       a.taux_frais, a.montant_achat, a.frais_achat, a.montant_total, 
       a.statut, a.date_achat,
       t.nom as type_nom, 
       b.quantite as besoin_quantite, 
       v.id as ville_id,
       v.nom as ville_nom, 
       c.nom as categorie_nom
FROM bngrc_achat a
LEFT JOIN bngrc_don d ON a.don_argent_id = d.id
LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id;

-- ============================
-- VUE: Achats en cours
-- ============================
CREATE OR REPLACE VIEW vue_achats_en_cours AS
SELECT a.id, a.don_argent_id, a.besoin_id, a.quantite, a.cout_unitaire, 
       a.taux_frais, a.montant_achat, a.frais_achat, a.montant_total, 
       a.date_achat,
       t.nom as type_nom, 
       b.quantite as besoin_quantite, 
       v.nom as ville_nom, 
       c.nom as categorie_nom
FROM bngrc_achat a
LEFT JOIN bngrc_don d ON a.don_argent_id = d.id
LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_ville v ON b.ville_id = v.id
WHERE a.statut = 'en_cours'
ORDER BY a.date_achat DESC;

-- ============================
-- VUE: Attributions complètes
-- ============================
CREATE OR REPLACE VIEW vue_attributions_complet AS
SELECT a.id, a.don_id, a.besoin_id, a.quantite_attribuee, a.date_dispatch,
       d.quantite as don_quantite, 
       b.quantite as besoin_quantite,
       t.nom as type_nom, 
       c.nom as categorie_nom,
       v.nom as ville_nom
FROM bngrc_attribution a
LEFT JOIN bngrc_don d ON a.don_id = d.id
LEFT JOIN bngrc_besoin b ON a.besoin_id = b.id
LEFT JOIN bngrc_type_besoin t ON d.type_besoin_id = t.id
LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
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
-- VUE: Besoins restants achètables
-- ============================
CREATE OR REPLACE VIEW vue_besoins_achatable AS
SELECT b.id, 
       b.quantite, 
       b.date_saisie,
       t.id as type_id, 
       t.nom as type_nom, 
       t.prix_unitaire,
       t.categorie_id,
       c.nom as categorie_nom,
       v.id as ville_id,
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
-- VUE: Types de besoins avec disponibilités
-- ============================
CREATE OR REPLACE VIEW vue_types_besoins_disponibles AS
SELECT t.id,
       t.nom,
       c.nom as categorie,
       t.prix_unitaire,
       COALESCE(SUM(d.quantite), 0) - COALESCE(SUM(a.quantite_attribuee), 0) AS quantite_totale
FROM bngrc_type_besoin t
LEFT JOIN bngrc_categorie c ON t.categorie_id = c.id
LEFT JOIN bngrc_don d ON d.type_besoin_id = t.id
LEFT JOIN bngrc_attribution a ON d.id = a.don_id
GROUP BY t.id, t.nom, c.nom, t.prix_unitaire
ORDER BY t.nom ASC;
