<?php

declare(strict_types=1);

$pdo = require __DIR__ . '/app/db.php';

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$crops = $pdo
    ->query('SELECT name, N, P2O5, K2O FROM crops ORDER BY name ASC')
    ->fetchAll();

$soilTypes = $pdo
    ->query('SELECT soil_class FROM soil_types ORDER BY id ASC')
    ->fetchAll();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Duengerpilot.de</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container">
    <section class="hero">
		<div class="logo-wrapper">
            <img src="assets/duengerpilot_logo.png" alt="Duengerpilot.de: Dein Düngemittel-Empfehlungssystem" class="site-logo">
        </div>
        <h1>Duengerpilot.de: Dein Düngemittel-Empfehlungssystem</h1>
        <p>
            Prototypische Web-App zur Demonstration eines digitalen Entscheidungsunterstützungssystems
            für Düngemittelmischungen. Die Anwendung bewertet Mischungen nach Kosten,
            Nährstoffabweichung und Umweltindikator.
        </p>
    </section>

    <section class="card">
        <h2>Eingabedaten</h2>
        <form method="post" action="results.php" class="form-grid" id="optimizer-form">
            <label>
                Kulturpflanze
                <select name="crop" id="crop" required>
                    <?php foreach ($crops as $crop): ?>
                        <option
                            value="<?= e($crop['name']) ?>"
                            data-n="<?= e($crop['N']) ?>"
                            data-p2o5="<?= e($crop['P2O5']) ?>"
                            data-k2o="<?= e($crop['K2O']) ?>"
                        >
                            <?= e($crop['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Bodenart
                <select name="soil" required>
                    <?php foreach ($soilTypes as $soilType): ?>
                        <option value="<?= e($soilType['soil_class']) ?>"><?= e($soilType['soil_class']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Feldgröße in ha
                <input type="number" name="field_size" min="0.1" max="10000" step="0.1" value="10" required>
            </label>

            <label>
                Zielwert Stickstoff (N in kg/ha)
                <input type="number" name="target_n" id="target_n" min="0" max="300" step="1" required>
            </label>

            <label>
                Zielwert Phosphor (P₂O₅ in kg/ha)
                <input type="number" name="target_p" id="target_p" min="0" max="200" step="1" required>
            </label>

            <label>
                Zielwert Kalium (K₂O in kg/ha)
                <input type="number" name="target_k" id="target_k" min="0" max="600" step="1" required>
            </label>

            <label>
                Maximale Anzahl Düngemittel-Produkte
                <select name="max_products">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5" selected>5</option>
                </select>
            </label>

            <label>
                max. Nährstoffabweichung in kg/ha
                <input type="number" name="epsilon_deviation" min="0" max="1000" step="1" value="80" required>
            </label>

            <label>
                Gewünschter Umwelt-Score als Schulnote
                <select name="epsilon_environment" required>
                    <option value="1.0">Note 1 - sehr geringe Umweltbelastung</option>
                    <option value="2.0">Note 2 - geringe Umweltbelastung</option>
                    <option value="3.0" selected>Note 3 - mittlere Umweltbelastung</option>
                    <option value="4.0">Note 4 - hohe Umweltbelastung</option>
                    <option value="5.0">Note 5 - sehr hohe Umweltbelastung</option>
                </select>
            </label>

            <button type="submit">Mischungen berechnen</button>
        </form>
        <div class="progress-panel" id="progress-panel" aria-live="polite" hidden>
            <div class="progress-header">
                <strong>Optimierung wird ausgeführt</strong>
                <span>Bitte warten...</span>
            </div>
            <div class="progress-bar" role="progressbar" aria-label="Optimierung wird ausgeführt">
                <div class="progress-bar-fill"></div>
            </div>
        </div>
    </section>

    <section class="card data-card">
        <h2>Verwendete Daten</h2>
        <p class="hint">
            Zeigt die Stammdaten an, die für die Berechnung der Düngemittelmischungen verwendet werden.
            Dabei handelt es sich ausschließlich um Beispielhafte Daten ohne Anspruch auf Richtigkeit und Vollständigkeit.
        </p>
        <div class="data-actions">
            <a href="fertilizers.php" class="data-button">Düngemittel</a>
            <a href="soil_types.php" class="data-button">Böden</a>
            <a href="crops.php" class="data-button">Kulturpflanzen</a>
            <a href="environmental_score.php" class="data-button">Umwelt-Score</a>
        </div>
    </section>

	<footer class="disclaimer">
        Die Anwendung ist ein Demonstrator. Sie ersetzt keine rechtsverbindliche Düngebedarfsermittlung
        und keine agronomische Beratung. Ziel ist die transparente Darstellung von Zielkonflikten zwischen
        Kosten, Nährstoffabweichung und Umweltindikator.
    </footer>
</main>
<script>
    const cropSelect = document.getElementById('crop');
    const targetN = document.getElementById('target_n');
    const targetP = document.getElementById('target_p');
    const targetK = document.getElementById('target_k');
    const optimizerForm = document.getElementById('optimizer-form');
    const progressPanel = document.getElementById('progress-panel');

    function updateCropNutrients() {
        const selectedCrop = cropSelect.options[cropSelect.selectedIndex];

        targetN.value = selectedCrop.dataset.n ?? '';
        targetP.value = selectedCrop.dataset.p2o5 ?? '';
        targetK.value = selectedCrop.dataset.k2o ?? '';
    }

    cropSelect.addEventListener('change', updateCropNutrients);
    optimizerForm.addEventListener('submit', () => {
        progressPanel.hidden = false;
    });
    updateCropNutrients();
</script>
</body>
</html>
