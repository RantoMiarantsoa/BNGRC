-- ============================
-- 1️⃣ SUPPRESSION SI EXISTE
-- ============================
DROP DATABASE IF EXISTS bngrc;

-- ============================
-- 2️⃣ CREATION BASE
-- ============================
CREATE DATABASE bngrc;
USE bngrc;


CREATE TABLE bngrc_region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- ============================
-- 4️⃣ TABLE VILLE
-- ============================
CREATE TABLE bngrc_ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    region_id INT NOT NULL,

    CONSTRAINT fk_ville_region
        FOREIGN KEY (region_id)
        REFERENCES bngrc_region(id)
        ON DELETE CASCADE
);

-- ============================
-- 5️⃣ TABLE TYPE BESOIN
-- ============================
CREATE TABLE bngrc_type_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    categorie ENUM('NATURE','MATERIAUX','ARGENT') NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    CHECK (prix_unitaire >= 0)
);

-- ============================
-- 6️⃣ TABLE BESOIN
-- ============================
CREATE TABLE bngrc_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    type_besoin_id INT NOT NULL,
    quantite INT NOT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,

    CHECK (quantite > 0),

    CONSTRAINT fk_besoin_ville
        FOREIGN KEY (ville_id)
        REFERENCES bngrc_ville(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_besoin_type
        FOREIGN KEY (type_besoin_id)
        REFERENCES bngrc_type_besoin(id)
        ON DELETE CASCADE
);

-- ============================
-- 7️⃣ TABLE DON
-- ============================
CREATE TABLE bngrc_don (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_besoin_id INT NOT NULL,
    quantite INT NOT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,

    CHECK (quantite > 0),

    CONSTRAINT fk_don_type
        FOREIGN KEY (type_besoin_id)
        REFERENCES bngrc_type_besoin(id)
        ON DELETE CASCADE
);

-- ============================
-- 8️⃣ TABLE ATTRIBUTION
-- ============================
CREATE TABLE bngrc_attribution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    besoin_id INT NOT NULL,
    quantite_attribuee INT NOT NULL,
    date_dispatch DATETIME DEFAULT CURRENT_TIMESTAMP,

    CHECK (quantite_attribuee > 0),

    CONSTRAINT fk_attribution_don
        FOREIGN KEY (don_id)
        REFERENCES bngrc_don(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_attribution_besoin
        FOREIGN KEY (besoin_id)
        REFERENCES bngrc_besoin(id)
        ON DELETE CASCADE
);

-- ============================
-- 9️⃣ INDEX POUR PERFORMANCE
-- ============================
CREATE INDEX idx_ville_region ON bngrc_ville(region_id);
CREATE INDEX idx_besoin_ville ON bngrc_besoin(ville_id);
CREATE INDEX idx_besoin_type ON bngrc_besoin(type_besoin_id);
CREATE INDEX idx_don_type ON bngrc_don(type_besoin_id);
CREATE INDEX idx_attr_don ON bngrc_attribution(don_id);
CREATE INDEX idx_attr_besoin ON bngrc_attribution(besoin_id);

