-- ============================
-- Ajouter la colonne 'ordre' à la table bngrc_besoin
-- À exécuter sur une base existante
-- ============================
USE bngrc;

ALTER TABLE bngrc_besoin ADD COLUMN ordre INT DEFAULT 0 AFTER quantite;

-- ============================
-- Mettre à jour l'ordre à partir des secondes de date_saisie
-- ============================
UPDATE bngrc_besoin SET ordre = SECOND(date_saisie);
