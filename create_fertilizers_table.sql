CREATE TABLE IF NOT EXISTS fertilizers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    short_name VARCHAR(40) NOT NULL,
    n_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
    p2o5_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
    k2o_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
    price_per_ton DECIMAL(10,2) NOT NULL,
    environmental_score DECIMAL(3,1) NOT NULL DEFAULT 1.0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_fertilizers_short_name (short_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO fertilizers
    (name, short_name, n_percent, p2o5_percent, k2o_percent, price_per_ton, environmental_score)
VALUES
    ('Kalkammonsalpeter', 'KAS', 27.00, 0.00, 0.00, 340.00, 3.0),
    ('Harnstoff', 'HARN', 46.00, 0.00, 0.00, 497.00, 4.0),
    ('Ammonsulfatsalpeter (ASS)', 'ASS', 26.00, 0.00, 0.00, 383.00, 3.2),
    ('Kalksalpeter', 'KSP', 15.50, 0.00, 0.00, 650.00, 2.8),
    ('Kalkstickstoff', 'Perlka', 19.80, 0.00, 0.00, 850.00, 4.5),
    ('Superphosphat', 'SSP', 0.00, 18.00, 0.00, 300.00, 1.8),
    ('Triple-Superphosphat', 'TSP', 0.00, 46.00, 0.00, 562.00, 2.0),
    ('Diammonphosphat', 'DAP', 18.00, 46.00, 0.00, 723.00, 3.0),
    ('Kaliumchlorid', '60er Kali', 0.00, 0.00, 60.00, 379.00, 1.4),
    ('Korn-Kali', 'Korn-Kali', 0.00, 0.00, 40.00, 307.00, 1.5),
    ('Patentkali', 'Patentkali', 0.00, 0.00, 30.00, 467.00, 1.6),
    ('Kaliumsulfat', 'Kali-Sul', 0.00, 0.00, 50.00, 650.00, 1.6),
    ('NPK Universal', 'NPK Uni', 15.00, 15.00, 15.00, 511.00, 3.0),
    ('NPK Getreide', 'NPK Getreide', 21.00, 6.00, 12.00, 520.00, 3.2),
    ('NPK Raps', 'NPK Raps', 16.00, 8.00, 16.00, 550.00, 3.1),
    ('NP Mais', 'NP Mais', 19.00, 17.00, 0.00, 590.00, 3.0),
    ('NK 16-32', 'NK 16-32', 16.00, 0.00, 32.00, 450.00, 2.8),
    ('PK 18-18', 'PK 18-18', 0.00, 18.00, 18.00, 430.00, 1.6),
    ('PK 12-24', 'PK 12-24', 0.00, 12.00, 24.00, 390.00, 1.6),
    ('Thomaskali', 'Thom-Ka', 0.00, 8.00, 15.00, 360.00, 1.5)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    n_percent = VALUES(n_percent),
    p2o5_percent = VALUES(p2o5_percent),
    k2o_percent = VALUES(k2o_percent),
    price_per_ton = VALUES(price_per_ton),
    environmental_score = VALUES(environmental_score);
