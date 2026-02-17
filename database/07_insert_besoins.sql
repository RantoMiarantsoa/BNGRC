USE bngrc;

-- ============================
-- REGIONS (si elles n'existent pas encore)
-- ============================
INSERT IGNORE INTO bngrc_region (nom) VALUES
('Atsinanana'),          -- Toamasina
('Vatovavy-Fitovinany'), -- Mananjary
('Atsimo-Atsinanana'),   -- Farafangana
('Diana'),               -- Nosy Be
('Menabe');              -- Morondava

-- ============================
-- VILLES (si elles n'existent pas encore)
-- ============================
INSERT IGNORE INTO bngrc_ville (nom, region_id) VALUES
('Toamasina',   (SELECT id FROM bngrc_region WHERE nom = 'Atsinanana' LIMIT 1)),
('Mananjary',   (SELECT id FROM bngrc_region WHERE nom = 'Vatovavy-Fitovinany' LIMIT 1)),
('Farafangana', (SELECT id FROM bngrc_region WHERE nom = 'Atsimo-Atsinanana' LIMIT 1)),
('Nosy Be',     (SELECT id FROM bngrc_region WHERE nom = 'Diana' LIMIT 1)),
('Morondava',   (SELECT id FROM bngrc_region WHERE nom = 'Menabe' LIMIT 1));

-- ============================
-- CATEGORIES (si elles n'existent pas encore)
-- ============================
INSERT IGNORE INTO bngrc_categorie (nom) VALUES
('nature'),
('materiel'),
('argent');

-- ============================
-- TYPES DE BESOIN (si ils n'existent pas encore)
-- ============================
INSERT IGNORE INTO bngrc_type_besoin (nom, categorie_id, prix_unitaire) VALUES
('Riz (kg)',      (SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   3000.00),
('Eau (L)',       (SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   1000.00),
('Huile (L)',     (SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   6000.00),
('Haricots',      (SELECT id FROM bngrc_categorie WHERE nom = 'nature' LIMIT 1),   4000.00),
('Tôle',          (SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 25000.00),
('Bâche',         (SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 15000.00),
('Clous (kg)',    (SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 8000.00),
('Bois',          (SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 10000.00),
('Groupe',        (SELECT id FROM bngrc_categorie WHERE nom = 'materiel' LIMIT 1), 6750000.00),
('Argent',        (SELECT id FROM bngrc_categorie WHERE nom = 'argent' LIMIT 1),   1.00);

-- ============================
-- BESOINS (avec colonne ordre)
-- ============================

-- Toamasina
INSERT INTO bngrc_besoin (ville_id, type_besoin_id, quantite, ordre, date_saisie) VALUES
((SELECT id FROM bngrc_ville WHERE nom = 'Toamasina' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Riz (kg)' LIMIT 1),
 800, 17, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Toamasina' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Eau (L)' LIMIT 1),
 1500, 4, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Toamasina' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Tôle' LIMIT 1),
 120, 23, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Toamasina' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Bâche' LIMIT 1),
 200, 1, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Toamasina' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Argent' LIMIT 1),
 12000000, 12, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Toamasina' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Groupe' LIMIT 1),
 3, 16, '2026-02-15 00:00:00'),

-- Mananjary
((SELECT id FROM bngrc_ville WHERE nom = 'Mananjary' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Riz (kg)' LIMIT 1),
 500, 9, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Mananjary' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Huile (L)' LIMIT 1),
 120, 25, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Mananjary' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Tôle' LIMIT 1),
 80, 6, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Mananjary' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Clous (kg)' LIMIT 1),
 60, 19, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Mananjary' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Argent' LIMIT 1),
 6000000, 3, '2026-02-15 00:00:00'),

-- Farafangana
((SELECT id FROM bngrc_ville WHERE nom = 'Farafangana' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Riz (kg)' LIMIT 1),
 600, 21, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Farafangana' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Eau (L)' LIMIT 1),
 1000, 14, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Farafangana' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Bâche' LIMIT 1),
 150, 8, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Farafangana' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Bois' LIMIT 1),
 100, 26, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Farafangana' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Argent' LIMIT 1),
 8000000, 10, '2026-02-16 00:00:00'),

-- Nosy Be
((SELECT id FROM bngrc_ville WHERE nom = 'Nosy Be' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Riz (kg)' LIMIT 1),
 300, 5, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Nosy Be' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Haricots' LIMIT 1),
 200, 18, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Nosy Be' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Tôle' LIMIT 1),
 40, 2, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Nosy Be' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Clous (kg)' LIMIT 1),
 30, 24, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Nosy Be' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Argent' LIMIT 1),
 4000000, 7, '2026-02-15 00:00:00'),

-- Morondava
((SELECT id FROM bngrc_ville WHERE nom = 'Morondava' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Riz (kg)' LIMIT 1),
 700, 11, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Morondava' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Eau (L)' LIMIT 1),
 1200, 20, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Morondava' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Bâche' LIMIT 1),
 180, 15, '2026-02-16 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Morondava' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Bois' LIMIT 1),
 150, 22, '2026-02-15 00:00:00'),

((SELECT id FROM bngrc_ville WHERE nom = 'Morondava' LIMIT 1),
 (SELECT id FROM bngrc_type_besoin WHERE nom = 'Argent' LIMIT 1),
 10000000, 13, '2026-02-16 00:00:00');
