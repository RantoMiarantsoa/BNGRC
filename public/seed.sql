USE bngrc;

INSERT INTO bngrc_region (nom) VALUES
('Analamanga'),
('Atsinanana'),
('Boeny');

INSERT INTO bngrc_ville (nom, region_id) VALUES
('Antananarivo', 1),
('Ambohidratrimo', 1),
('Toamasina', 2),
('Mahajanga', 3);

INSERT INTO bngrc_categorie (nom) VALUES
('NATURE'),
('MATERIAUX'),
('ARGENT');

INSERT INTO bngrc_type_besoin (nom, categorie_id, prix_unitaire) VALUES
('Riz', 1, 2500.00),
('Eau potable', 1, 1000.00),
('Couvertures', 2, 15000.00),
('Tentes', 2, 75000.00),
('Fonds d''urgence', 3, 1.00);

INSERT INTO bngrc_besoin (ville_id, type_besoin_id, quantite, date_saisie) VALUES
(1, 1, 500, '2026-02-10 09:00:00'),
(1, 2, 300, '2026-02-11 10:00:00'),
(2, 3, 120, '2026-02-12 11:00:00'),
(3, 1, 800, '2026-02-13 12:00:00'),
(3, 4, 40,  '2026-02-13 14:00:00'),
(4, 2, 250, '2026-02-14 08:30:00'),
(4, 5, 100000, '2026-02-14 09:15:00');

INSERT INTO bngrc_don (type_besoin_id, quantite, date_saisie) VALUES
(1, 600, '2026-02-12 13:00:00'),
(2, 180, '2026-02-12 13:30:00'),
(3, 90,  '2026-02-12 15:00:00'),
(4, 20,  '2026-02-12 16:00:00'),
(5, 50000, '2026-02-12 17:00:00');

INSERT INTO bngrc_attribution (don_id, besoin_id, quantite_attribuee, date_dispatch) VALUES
(1, 1, 300, '2026-02-13 09:00:00'),
(1, 4, 200, '2026-02-13 10:00:00'),
(2, 2, 120, '2026-02-13 11:00:00'),
(3, 3, 70,  '2026-02-13 13:00:00'),
(4, 5, 10,  '2026-02-13 14:00:00'),
(5, 7, 20000, '2026-02-13 16:00:00');

