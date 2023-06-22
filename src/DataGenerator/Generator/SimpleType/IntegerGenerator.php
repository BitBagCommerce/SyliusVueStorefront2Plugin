<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\SimpleType;

class IntegerGenerator implements IntegerGeneratorInterface
{
    public static function generateBiased(
        int $bias,
        int $min,
        int $max,
    ): int {
        $range = $max - $min + 1;
        $topValuesThreshold = $min + (int)($range * self::TOP_VALUES_THRESHOLD_FACTOR);

        if (mt_rand() / mt_getrandmax() <= $bias / 100) {
            return mt_rand($topValuesThreshold, $max);
        }

        return mt_rand($min, $topValuesThreshold - 1);
    }
}
