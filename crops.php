<?php

declare(strict_types=1);

$pdo = require __DIR__ . '/app/db.php';

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$stmt = $pdo->query(
    'SELECT name, N, P2O5, K2O, Unit
     FROM crops
     ORDER BY name ASC'
);
$crops = $stmt->fetchAll();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kulturpflanzen</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container">
    <section class="hero compact">
        <h1>Kulturpflanzen</h1>
        <p>
            Nährstoffbedarf der in der App verwendeten Kulturpflanzen.
            Dabei handelt es sich ausschließlich um Beispielhafte Daten ohne Anspruch auf Richtigkeit und Vollständigkeit.
        </p>
        <a href="index.php" class="back-link">Zurück zur Hauptseite</a>
    </section>

    <section class="card">
        <h2>Stammdaten Kulturpflanzen</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Kulturpflanze</th>
                    <th>N</th>
                    <th>P2O5</th>
                    <th>K2O</th>
                    <th>Einheit</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($crops as $crop): ?>
                    <tr>
                        <td><?= e($crop['name']) ?></td>
                        <td><?= e($crop['N']) ?></td>
                        <td><?= e($crop['P2O5']) ?></td>
                        <td><?= e($crop['K2O']) ?></td>
                        <td><?= e($crop['Unit']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
