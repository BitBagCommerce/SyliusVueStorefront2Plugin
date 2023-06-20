<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Bulk;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Bulk\BulkGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\EntityContextInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Style\SymfonyStyle;

final class BulkGeneratorContextSpec extends ObjectBehavior
{
    private const QUANTITY = 100;

    public function let(
        SymfonyStyle $io,
        EntityContextInterface $context,
    ): void {
        $this->beConstructedWith(self::QUANTITY, $io, $context);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BulkGeneratorContext::class);
    }

    public function it_returns_quantity(): void
    {
        $this->getQuantity()->shouldReturn(self::QUANTITY);
    }

    public function it_returns_io(SymfonyStyle $io): void
    {
        $this->getIo()->shouldReturn($io);
    }

    public function it_returns_context(EntityContextInterface $context): void
    {
        $this->getEntityContext()->shouldReturn($context);
    }
}
