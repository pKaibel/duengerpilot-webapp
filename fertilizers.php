<?php

declare(strict_types=1);

$pdo = require __DIR__ . '/app/db.php';

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$stmt = $pdo->query(
    'SELECT name, short_name, n_percent, p2o5_percent, k2o_percent, price_per_ton, environmental_score
     FROM fertilizers
     ORDER BY name ASC'
);
$fertilizers = $stmt->fetchAll();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Düngemittel</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container">
    <section class="hero compact">
        <h1>Düngemittel</h1>
        <p>
            Stammdaten der in der App verwendeten Düngemittel.
            Dabei handelt es sich ausschließlich um Beispielhafte Daten ohne Anspruch auf Richtigkeit und Vollständigkeit.
        </p>
        <a href="index.php" class="back-link">Zurück zur Hauptseite</a>
    </section>

    <section class="card">
        <h2>Stammdaten Düngemittel</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Düngemittel</th>
                    <th>Kurzname</th>
                    <th>N in %</th>
                    <th>P2O5 in %</th>
                    <th>K2O in %</th>
                    <th>Preis €/t</th>
                    <th>Umwelt-Score</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($fertilizers as $fertilizer): ?>
                    <tr>
                        <td><?= e($fertilizer['name']) ?></td>
                        <td><?= e($fertilizer['short_name']) ?></td>
                        <td><?= e($fertilizer['n_percent']) ?></td>
                        <td><?= e($fertilizer['p2o5_percent']) ?></td>
                        <td><?= e($fertilizer['k2o_percent']) ?></td>
                        <td><?= e($fertilizer['price_per_ton']) ?></td>
                        <td><?= e($fertilizer['environmental_score']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
