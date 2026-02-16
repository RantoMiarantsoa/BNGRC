-- ============================
-- 🔟 TABLE CONFIGURATION (TAUX FRAIS)
-- ============================
CREATE TABLE bngrc_configuration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(50) NOT NULL UNIQUE,
    valeur DECIMAL(15,2)
);

-- ============================
-- 1️⃣1️⃣ TABLE ACHAT
-- ============================
CREATE TABLE bngrc_achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_argent_id INT NOT NULL,
    besoin_id INT NOT NULL,
    quantite INT NOT NULL,
    cout_unitaire DECIMAL(15,2) NOT NULL,
    taux_frais DECIMAL(5,2) NOT NULL DEFAULT 0,
    montant_achat DECIMAL(15,2) NOT NULL,
    frais_achat DECIMAL(15,2) NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL,
    statut ENUM('en_cours', 'finalisé', 'annulé') DEFAULT 'en_cours',
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,

    CHECK (quantite > 0),
    CHECK (cout_unitaire >= 0),
    CHECK (taux_frais >= 0),
    CHECK (montant_total > 0),

    CONSTRAINT fk_achat_don
        FOREIGN KEY (don_argent_id)
        REFERENCES bngrc_don(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_achat_besoin
        FOREIGN KEY (besoin_id)
        REFERENCES bngrc_besoin(id)
        ON DELETE CASCADE
);

-- ============================
-- INDEX POUR PERFORMANCE
-- ============================
CREATE INDEX idx_achat_don ON bngrc_achat(don_argent_id);
CREATE INDEX idx_achat_besoin ON bngrc_achat(besoin_id);
CREATE INDEX idx_achat_statut ON bngrc_achat(statut);

-- ============================
-- CONFIGURATION ACHATS
-- ============================

