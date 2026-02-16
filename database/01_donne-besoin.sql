INSERT INTO bngrc_categorie (nom) VALUES
('NATURE'),
('MATERIELS'),
('ARGENTS');

INSERT INTO bngrc_type_besoin (nom, categorie_id, prix_unitaire) VALUES

-- ========================
-- NATURE (id = 1)
-- ========================
('Riz (kg)', 1, 2500.00),
('Eau potable (L)', 1, 1000.00),
('Huile alimentaire (L)', 1, 6000.00),
('Médicaments de base (boîte)', 1, 50000.00),

-- ========================
-- MATERIELS (id = 2)
-- ========================
('Tente familiale', 2, 350000.00),
('Bâche de protection', 2, 45000.00),
('Kit scolaire', 2, 40000.00),
('Kit d’hygiène', 2, 30000.00),

-- ========================
-- ARGENTS (id = 3)
-- ========================
('Aide financière urgente', 3, NULL),
('Subvention reconstruction', 3, NULL);

INSERT INTO bngrc_region (nom) VALUES
('Analamanga'),
('Atsinanana'),
('Boeny'),
('Vakinankaratra'),
('Haute Matsiatra'),
('Atsimo-Andrefana'),
('Diana'),
('Sava');

INSERT INTO bngrc_ville (nom, region_id) VALUES

-- Analamanga (id = 1)
('Antananarivo', 1),

-- Atsinanana (id = 2)
('Toamasina', 2),

-- Boeny (id = 3)
('Mahajanga', 3),

-- Vakinankaratra (id = 4)
('Antsirabe', 4),

-- Haute Matsiatra (id = 5)
('Fianarantsoa', 5),

-- Atsimo-Andrefana (id = 6)
('Toliara', 6),

-- Diana (id = 7)
('Antsiranana', 7),

-- Sava (id = 8)
('Sambava', 8);

INSERT INTO bngrc_besoin (ville_id, type_besoin_id, quantite) VALUES

-- Antananarivo (ville_id = 1)
(1, 1, 800),        -- 800 kg Riz
(1, 5, 20),         -- 20 Tentes
(1, 9, 2000000),    -- 2 000 000 Ar Aide financière

-- Toamasina (ville_id = 2)
(2, 2, 1500),       -- 1500 L Eau potable
(2, 6, 50),         -- 50 Bâches
(2, 10, 5000000),   -- 5 000 000 Ar Subvention reconstruction

-- Mahajanga (ville_id = 3)
(3, 3, 400),        -- 400 L Huile alimentaire
(3, 7, 100),        -- 100 Kits scolaires
(3, 9, 1000000);    -- 1 000 000 Ar Aide financière
   -- 1 000 000 Ar Aide financière d’urgence
