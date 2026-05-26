CREATE TABLE IF NOT EXISTS soil_types (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    soil_class VARCHAR(120) NOT NULL,
    n_correction_kg_ha DECIMAL(8,2) NOT NULL,
    pk_multiplier DECIMAL(4,2) NOT NULL,
    reason TEXT NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_soil_types_soil_class (soil_class)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO soil_types (soil_class, n_correction_kg_ha, pk_multiplier, reason)
VALUES
    ('Leichte Böden (z.B. Sand)', 30, 1.0, 'Geringe N-Nachlieferung bei wenig Humus; höheres Auswaschungsrisiko.'),
    ('Mittlere Böden (z.B. Lehm)', 0, 1.0, 'Standardbedingungen für die Düngungsnorm.'),
    ('Schwere Böden (z.B. Ton)', -40, 1.0, 'Höheres Mineralisierungspotenzial der organischen Substanz.'),
    ('Humusreiche Böden (≥ 20% OS)', -100, 0.7, 'Massive Stickstoffnachlieferung aus dem Bodenvorrat.')
ON DUPLICATE KEY UPDATE
    n_correction_kg_ha = VALUES(n_correction_kg_ha),
    pk_multiplier = VALUES(pk_multiplier),
    reason = VALUES(reason);
