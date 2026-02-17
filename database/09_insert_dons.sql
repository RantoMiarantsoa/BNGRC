USE bngrc;

-- ============================
-- INSERTION DES DONS
-- ============================
INSERT INTO bngrc_don (id_type_categorie, nom, quantite, date_saisie) VALUES
((SELECT id FROM bngrc_categorie WHERE nom = 'argent' LIMIT 1),   'Argent',      5000000,  '2026-02-16 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'argent' LIMIT 1),   'Argent',      3000000,  '2026-02-16 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'argent' LIMIT 1),   'Argent',      4000000,  '2026-02-17 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'argent' LIMIT 1),   'Argent',      1500000,  '2026-02-17 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'argent' LIMIT 1),   'Argent',      6000000,  '2026-02-17 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   'Riz (kg)',    400,      '2026-02-16 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   'Eau (L)',     600,      '2026-02-16 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 'Tôle',        50,       '2026-02-17 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 'Bâche',       70,       '2026-02-17 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   'Haricots',    100,      '2026-02-17 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   'Riz (kg)',    2000,     '2026-02-18 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 'Tôle',        300,      '2026-02-18 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   'Eau (L)',     5000,     '2026-02-18 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'argent' LIMIT 1),   'Argent',      20000000, '2026-02-19 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 'Bâche',       500,      '2026-02-19 00:00:00'),
((SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   'Haricots',    88,       '2026-02-17 00:00:00');
