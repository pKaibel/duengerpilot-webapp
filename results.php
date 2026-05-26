<?php

declare(strict_types=1);

require __DIR__ . '/app/fertilizer_repository.php';
require __DIR__ . '/app/optimizer.php';

$pdo = require __DIR__ . '/app/db.php';

function postFloat(string $key, float $default = 0): float
{
    return isset($_POST[$key]) ? max(0, (float) $_POST[$key]) : $default;
}

function postInt(string $key, int $default = 0): int
{
    return isset($_POST[$key]) ? max(0, (int) $_POST[$key]) : $default;
}

function postPositiveFloat(string $key, float $default = 1): float
{
    return isset($_POST[$key]) ? max(0.1, (float) $_POST[$key]) : $default;
}

function e(string|int|float $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function getSelectedSoilType(PDO $pdo, string $soil): array
{
    $stmt = $pdo->prepare(
        'SELECT soil_class, n_correction_kg_ha, pk_multiplier, reason
         FROM soil_types
         WHERE soil_class = :soil_class
         LIMIT 1'
    );
    $stmt->execute(['soil_class' => $soil]);

    return $stmt->fetch() ?: [
        'soil_class' => $soil,
        'n_correction_kg_ha' => 0,
        'pk_multiplier' => 1,
        'reason' => 'Keine Bodenklasse in der Datenbank gefunden.',
    ];
}

$crop = trim((string) ($_POST['crop'] ?? 'unbekannt'));
$soil = trim((string) ($_POST['soil'] ?? 'unbekannt'));
$targetN = postFloat('target_n', 160);
$targetP = postFloat('target_p', 60);
$targetK = postFloat('target_k', 90);
$fieldSize = postPositiveFloat('field_size', 10);
$maxProducts = min(5, max(1, postInt('max_products', 5)));
$epsilonDeviation = postFloat('epsilon_deviation', 80);
$epsilonEnvironment = min(5, max(1, postFloat('epsilon_environment', 3.0)));

$soilType = getSelectedSoilType($pdo, $soil);
$nCorrection = (float) $soilType['n_correction_kg_ha'];
$pkMultiplier = (float) $soilType['pk_multiplier'];
$adjustedTargetN = max(0, $targetN + $nCorrection);
$adjustedTargetP = max(0, $targetP * $pkMultiplier);
$adjustedTargetK = max(0, $targetK * $pkMultiplier);

$fertilizers = getAllFertilizers($pdo);
$solutions = optimizeFertilizerMix(
    $fertilizers,
    $adjustedTargetN,
    $adjustedTargetP,
    $adjustedTargetK,
    $maxProducts,
    $fieldSize,
    $epsilonDeviation,
    $epsilonEnvironment,
    25,
    400,
    20
);
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berechnungsergebnisse</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container">
    <section class="hero compact">
        <h1>Berechnungsergebnisse</h1>
        <p>
            Kultur: <strong><?= e($crop) ?></strong> ·
            Bodenart: <strong><?= e($soil) ?></strong> ·
            Feldgröße: <strong><?= e($fieldSize) ?> ha</strong> ·
            Zielwerte gesamt: N <?= e(round($targetN * $fieldSize, 2)) ?> kg,
            P₂O₅ <?= e(round($targetP * $fieldSize, 2)) ?> kg,
            K₂O <?= e(round($targetK * $fieldSize, 2)) ?> kg
        </p>
        <p>
            Bodenanpassung gesamt: N <?= e(round($nCorrection * $fieldSize, 2)) ?> kg,
            P/K-Faktor <?= e($pkMultiplier) ?> Â·
            Optimierungsziel gesamt: N <?= e(round($adjustedTargetN * $fieldSize, 2)) ?> kg,
            Pâ‚‚Oâ‚… <?= e(round($adjustedTargetP * $fieldSize, 2)) ?> kg,
            Kâ‚‚O <?= e(round($adjustedTargetK * $fieldSize, 2)) ?> kg
        </p>
        <p>
            ε-Constraints: Nährstoffabweichung ≤ <?= e(round($epsilonDeviation * $fieldSize, 2)) ?> kg gesamt,
            Umwelt-Score ≤ <?= e($epsilonEnvironment) ?>.
            Innerhalb dieser Grenzen werden die Gesamtkosten minimiert.
        </p>
        <a href="index.php" class="back-link">Neue Berechnung starten</a>
    </section>

    <section class="card">
        <h2>Gefundene Mischungen</h2>
        <p class="hint">
            Die ε-Constraint-Methode minimiert die Gesamtkosten und berücksichtigt Nährstoffabweichung
            sowie Umwelt-Score als verbindliche Grenzwerte.
        </p>

        <?php if (count($solutions) === 0): ?>
            <p>Es wurden keine zulässigen Lösungen gefunden. Bitte Zielwerte, Produktanzahl oder ε-Grenzen anpassen.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Nr.</th>
                        <th>Mischung</th>
                        <th>N</th>
                        <th>P₂O₅</th>
                        <th>K₂O</th>
                        <th>Kosten</th>
                        <th>Abweichung</th>
                        <th>Umwelt</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($solutions as $index => $solution): ?>
                        <tr class="<?= $solution['pareto'] ? 'pareto' : '' ?>">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php foreach ($solution['items'] as $item): ?>
                                    <div>
                                        <strong><?= e($item['name']) ?></strong>:
                                        <?= e($item['amount_kg']) ?> kg gesamt
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td><?= e($solution['evaluation']['total_n']) ?> kg gesamt</td>
                            <td><?= e($solution['evaluation']['total_p']) ?> kg gesamt</td>
                            <td><?= e($solution['evaluation']['total_k']) ?> kg gesamt</td>
                            <td><?= e($solution['evaluation']['cost']) ?> € gesamt</td>
                            <td><?= e($solution['evaluation']['nutrient_deviation']) ?> kg gesamt</td>
                            <td><?= e($solution['evaluation']['environmental_score']) ?></td>
                            <td>
                                <?php if ($solution['pareto']): ?>
                                    <span class="badge">Pareto-effizient</span>
                                <?php else: ?>
                                    <span class="muted">dominiert</span>
                                <?php endif; ?>
                                <div class="status-detail">
                                    Bereich: <?= e(implode(', ', $solution['dominance_areas'])) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
