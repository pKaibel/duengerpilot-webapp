<?php

declare(strict_types=1);

/**
 * Erzeugt Düngemittelmischungen in diskreten Mengenschritten.
 * Die Kosten werden minimiert, während Nährstoffabweichung und Umwelt-Score als
 * ε-Constraints begrenzt werden.
 */
function optimizeFertilizerMix(
    array $fertilizers,
    float $targetN,
    float $targetP,
    float $targetK,
    int $maxProducts = 5,
    float $fieldSizeHa = 1.0,
    float $epsilonDeviationKgHa = 120.0,
    float $epsilonEnvironment = 3.5,
    int $stepKg = 25,
    int $maxAmountKg = 400,
    int $resultLimit = 20
): array {
    $solutions = [];
    $count = count($fertilizers);

    for ($i = 0; $i < $count; $i++) {
        for ($j = $i; $j < $count; $j++) {
            for ($k = $j; $k < $count; $k++) {
                $selected = uniqueFertilizers([$fertilizers[$i], $fertilizers[$j], $fertilizers[$k]]);

                if (count($selected) > $maxProducts) {
                    continue;
                }

                foreach (generateAmounts(count($selected), $stepKg, $maxAmountKg) as $amounts) {
                    $items = [];
                    foreach ($selected as $index => $fertilizer) {
                        if ($amounts[$index] <= 0) {
                            continue;
                        }
                        $fertilizer['amount_kg_ha'] = $amounts[$index];
                        $fertilizer['amount_kg'] = round($amounts[$index] * $fieldSizeHa, 2);
                        $items[] = $fertilizer;
                    }

                    if (count($items) === 0 || count($items) > $maxProducts) {
                        continue;
                    }

                    $evaluation = evaluateSolution($items, $targetN, $targetP, $targetK, $fieldSizeHa);

                    if (
                        $evaluation['nutrient_deviation'] > $epsilonDeviationKgHa * $fieldSizeHa ||
                        $evaluation['environmental_score'] > $epsilonEnvironment
                    ) {
                        continue;
                    }

                    $solutions[] = [
                        'items' => $items,
                        'evaluation' => $evaluation,
                        'score' => $evaluation['cost'],
                    ];
                }
            }
        }
    }

    $solutions = removeDuplicateSolutions($solutions);
    $solutions = markParetoEfficient($solutions);
    $solutions = annotateDominanceAreas($solutions);

    usort($solutions, static function (array $a, array $b): int {
        return
            ($a['evaluation']['cost'] <=> $b['evaluation']['cost']) ?:
            ($a['evaluation']['nutrient_deviation'] <=> $b['evaluation']['nutrient_deviation']) ?:
            ($a['evaluation']['environmental_score'] <=> $b['evaluation']['environmental_score']);
    });

    return array_slice($solutions, 0, $resultLimit);
}

function uniqueFertilizers(array $fertilizers): array
{
    $seen = [];
    $result = [];

    foreach ($fertilizers as $fertilizer) {
        if (!isset($seen[$fertilizer['id']])) {
            $seen[$fertilizer['id']] = true;
            $result[] = $fertilizer;
        }
    }

    return $result;
}

function generateAmounts(int $productCount, int $stepKg, int $maxAmountKg): array
{
    $amounts = range(0, $maxAmountKg, $stepKg);
    $combinations = [];

    if ($productCount === 1) {
        foreach ($amounts as $a) {
            $combinations[] = [$a];
        }
    } elseif ($productCount === 2) {
        foreach ($amounts as $a) {
            foreach ($amounts as $b) {
                $combinations[] = [$a, $b];
            }
        }
    } else {
        foreach ($amounts as $a) {
            foreach ($amounts as $b) {
                foreach ($amounts as $c) {
                    $combinations[] = [$a, $b, $c];
                }
            }
        }
    }

    return $combinations;
}

function evaluateSolution(array $items, float $targetN, float $targetP, float $targetK, float $fieldSizeHa = 1.0): array
{
    $totalN = 0.0;
    $totalP = 0.0;
    $totalK = 0.0;
    $cost = 0.0;
    $environmentWeighted = 0.0;
    $totalAmount = 0.0;

    foreach ($items as $item) {
        $amountKg = (float) $item['amount_kg_ha'];

        $totalN += $amountKg * ((float) $item['n_percent'] / 100);
        $totalP += $amountKg * ((float) $item['p_percent'] / 100);
        $totalK += $amountKg * ((float) $item['k_percent'] / 100);

        $cost += ($amountKg / 1000) * (float) $item['price_per_ton'];
        $environmentWeighted += $amountKg * (float) ($item['environmental_score'] ?? 1);
        $totalAmount += $amountKg;
    }

    $nutrientDeviation =
        abs($targetN - $totalN) +
        abs($targetP - $totalP) +
        abs($targetK - $totalK);

    $avgEnvironment = $totalAmount > 0 ? $environmentWeighted / $totalAmount : 0;

    return [
        'total_n' => round($totalN * $fieldSizeHa, 2),
        'total_p' => round($totalP * $fieldSizeHa, 2),
        'total_k' => round($totalK * $fieldSizeHa, 2),
        'cost' => round($cost * $fieldSizeHa, 2),
        'nutrient_deviation' => round($nutrientDeviation * $fieldSizeHa, 2),
        'environmental_score' => round($avgEnvironment, 2),
        'total_amount' => round($totalAmount * $fieldSizeHa, 2),
        'field_size_ha' => round($fieldSizeHa, 2),
    ];
}

function removeDuplicateSolutions(array $solutions): array
{
    $seen = [];
    $unique = [];

    foreach ($solutions as $solution) {
        $parts = [];
        foreach ($solution['items'] as $item) {
            $parts[] = $item['id'] . ':' . $item['amount_kg_ha'];
        }
        sort($parts);
        $key = implode('|', $parts);

        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $unique[] = $solution;
        }
    }

    return $unique;
}

function markParetoEfficient(array $solutions): array
{
    foreach ($solutions as $i => $solution) {
        $solutions[$i]['pareto'] = true;

        foreach ($solutions as $j => $other) {
            if ($i === $j) {
                continue;
            }

            if (dominates($other['evaluation'], $solution['evaluation'])) {
                $solutions[$i]['pareto'] = false;
                break;
            }
        }
    }

    return $solutions;
}

function annotateDominanceAreas(array $solutions): array
{
    if (count($solutions) === 0) {
        return $solutions;
    }

    $minCost = min(array_column(array_column($solutions, 'evaluation'), 'cost'));
    $minDeviation = min(array_column(array_column($solutions, 'evaluation'), 'nutrient_deviation'));
    $minEnvironment = min(array_column(array_column($solutions, 'evaluation'), 'environmental_score'));
    $epsilon = 0.00001;

    foreach ($solutions as $index => $solution) {
        $evaluation = $solution['evaluation'];
        $areas = [];

        if ($evaluation['cost'] <= $minCost + $epsilon) {
            $areas[] = 'Kosten';
        }

        if ($evaluation['nutrient_deviation'] <= $minDeviation + $epsilon) {
            $areas[] = 'Nährstoffabweichung';
        }

        if ($evaluation['environmental_score'] <= $minEnvironment + $epsilon) {
            $areas[] = 'Umwelt';
        }

        if (count($areas) === 0 && $solution['pareto']) {
            $areas[] = 'ausgewogener Kompromiss';
        }

        if (count($areas) === 0) {
            $areas[] = 'kein dominanter Bereich';
        }

        $solutions[$index]['dominance_areas'] = $areas;
    }

    return $solutions;
}

function dominates(array $a, array $b): bool
{
    $aBetterOrEqual =
        $a['cost'] <= $b['cost'] &&
        $a['nutrient_deviation'] <= $b['nutrient_deviation'] &&
        $a['environmental_score'] <= $b['environmental_score'];

    $aStrictlyBetter =
        $a['cost'] < $b['cost'] ||
        $a['nutrient_deviation'] < $b['nutrient_deviation'] ||
        $a['environmental_score'] < $b['environmental_score'];

    return $aBetterOrEqual && $aStrictlyBetter;
}
