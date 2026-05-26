CREATE TABLE IF NOT EXISTS crops (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    N DECIMAL(8,2) NOT NULL,
    P2O5 DECIMAL(8,2) NOT NULL,
    K2O DECIMAL(8,2) NOT NULL,
    Unit VARCHAR(20) NOT NULL DEFAULT 'kg/ha',
    PRIMARY KEY (id),
    UNIQUE KEY uq_crops_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO crops (name, N, P2O5, K2O, Unit)
VALUES
    ('Winterweizen', 235, 63, 109, 'kg/ha'),
    ('Wintergerste', 145, 64, 103, 'kg/ha'),
    ('Mais', 155, 103, 215, 'kg/ha'),
    ('Speisekartoffeln', 180, 82, 448, 'kg/ha'),
    ('Zuckerrüben', 135, 92, 383, 'kg/ha'),
    ('Winterraps', 175, 69, 202, 'kg/ha')
ON DUPLICATE KEY UPDATE
    N = VALUES(N),
    P2O5 = VALUES(P2O5),
    K2O = VALUES(K2O),
    Unit = VALUES(Unit);
