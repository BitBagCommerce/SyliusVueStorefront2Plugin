<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Bulk;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Bulk\BulkContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\EntityContextInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Style\SymfonyStyle;

final class BulkContextSpec extends ObjectBehavior
{
    public function let(
        SymfonyStyle $io,
        EntityContextInterface $context,
    ): void {
        $this->beConstructedWith(100, $io, $context);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BulkContext::class);
    }
}
