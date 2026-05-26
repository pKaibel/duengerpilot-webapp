<?php

declare(strict_types=1);

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$scoreRows = [
    ['PK-Dünger ohne Stickstoff', '1.0 - 1.5'],
    ['Kalidünger', '1.2 - 1.8'],
    ['Phosphatdünger', '1.5 - 2.2'],
    ['NPK-Dünger', '2.5 - 3.5'],
    ['Kalkammonsalpeter / Nitratdünger', '3.0'],
    ['Ammonsulfatsalpeter', '3.2'],
    ['Harnstoff', '4.0'],
    ['AHL / flüssige N-Dünger', '4.0 - 4.5'],
    ['Kalkstickstoff', '4.5 - 5.0'],
];
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Umwelt-Score</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container">
    <section class="hero compact">
        <h1>Umwelt-Score</h1>
        <p>
            Herleitung des relativen Demonstrator-Scores für die Umweltbewertung.
            Dabei handelt es sich ausschließlich um Beispielhafte Daten ohne Anspruch auf Richtigkeit und Vollständigkeit.
        </p>
        <a href="index.php" class="back-link">Zurück zur Hauptseite</a>
    </section>

    <section class="card">
        <h2>Score-Skala</h2>
        <div class="score-scale">
            <div><strong>1.0</strong> = sehr geringe Umweltbelastung</div>
            <div><strong>2.0</strong> = gering</div>
            <div><strong>3.0</strong> = mittel</div>
            <div><strong>4.0</strong> = hoch</div>
            <div><strong>5.0</strong> = sehr hoch</div>
        </div>
    </section>

    <section class="card data-card">
        <h2>Beispielhafte Einordnung</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Düngertyp</th>
                    <th>Umwelt-Score</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($scoreRows as [$type, $score]): ?>
                    <tr>
                        <td><?= e($type) ?></td>
                        <td><?= e($score) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="card data-card">
        <h2>Warum Stickstoffdünger höher bewertet werden</h2>
        <p>
            Stickstoffdünger erhalten in diesem einfachen Demonstrator meist einen höheren Umwelt-Score,
            weil ihre Herstellung energieintensiv ist und im Pflanzenbau zusätzlich klimawirksame Verluste
            entstehen können. Besonders relevant sind Lachgasemissionen (N₂O), Ammoniakverluste,
            Nitratverlagerung und die CO₂-Emissionen aus Produktion, Transport und Anwendung.
        </p>
        <p>
            Reine P- und K-Dünger werden hier niedriger eingeordnet, weil sie keinen mineralischen Stickstoff
            einbringen und damit die typischen N-bezogenen Emissions- und Auswaschungsrisiken in dieser
            vereinfachten Bewertung nicht oder deutlich weniger stark auslösen. Der Score ist bewusst relativ
            und dient nur dazu, Zielkonflikte zwischen Kosten, Nährstofftreffgenauigkeit und Umweltwirkung
            im Demonstrator sichtbar zu machen.
        </p>
    </section>
</main>
</body>
</html>
