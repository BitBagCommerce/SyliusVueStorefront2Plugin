<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\SimpleType\Integer;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\SimpleType\Integer\IntegerGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\SimpleType\Integer\RandInterface;
use PhpSpec\ObjectBehavior;

class IntegerGeneratorSpec extends ObjectBehavior
{
    public function let(RandInterface $rand): void
    {
        $this->beConstructedWith($rand);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(IntegerGenerator::class);
    }

    public function it_returns_values_under_top_values_threshold(RandInterface $rand): void
    {
        $min = 1;
        $max = 150;
        $bias = 80;
        $topValuesThreshold = 80;
        $topValuesThresholdCalculated = 121;

        $rand->rand()->willReturn(1723841632);
        $rand->randMax()->willReturn(2147483647);

        $rand->rand($min, $topValuesThresholdCalculated - 1)->willReturn(15);

        $this->generateBiased($min, $max, $bias, $topValuesThreshold)->shouldReturn(15);
    }

    public function it_returns_int_within_top_values_threshold(RandInterface $rand): void
    {
        $min = 1;
        $max = 150;
        $bias = 30;
        $topValuesThreshold = 80;
        $topValuesThresholdCalculated = 121;

        $rand->rand()->willReturn(18264256);
        $rand->randMax()->willReturn(2147483647);

        $rand->rand($topValuesThresholdCalculated, $max)->willReturn(132);

        $this->generateBiased($min, $max, $bias, $topValuesThreshold)->shouldReturn(132);
    }
}
