<?php

declare(strict_types=1);

$pdo = require __DIR__ . '/app/db.php';

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$stmt = $pdo->query(
    'SELECT soil_class, n_correction_kg_ha, pk_multiplier, reason
     FROM soil_types
     ORDER BY id ASC'
);
$soilTypes = $stmt->fetchAll();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Böden</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container">
    <section class="hero compact">
        <h1>Böden</h1>
        <p>
            Stickstoff-Korrektur und P/K-Multiplikator je Bodenklasse.
            Dabei handelt es sich ausschließlich um Beispielhafte Daten ohne Anspruch auf Richtigkeit und Vollständigkeit.
        </p>
        <a href="index.php" class="back-link">Zurück zur Hauptseite</a>
    </section>

    <section class="card">
        <h2>Stammdaten Böden</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Bodenklasse</th>
                    <th>N-Korrektur kg/ha</th>
                    <th>Multiplikator P/K</th>
                    <th>Begründung</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($soilTypes as $soilType): ?>
                    <tr>
                        <td><?= e($soilType['soil_class']) ?></td>
                        <td><?= e($soilType['n_correction_kg_ha']) ?></td>
                        <td><?= e($soilType['pk_multiplier']) ?></td>
                        <td><?= e($soilType['reason']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
