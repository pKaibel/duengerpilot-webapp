<?php

declare(strict_types=1);

function getAllFertilizers(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT id, name, n_percent, p2o5_percent AS p_percent, k2o_percent AS k_percent, price_per_ton, environmental_score
         FROM fertilizers
         ORDER BY name ASC'
    );

    return $stmt->fetchAll();
}
