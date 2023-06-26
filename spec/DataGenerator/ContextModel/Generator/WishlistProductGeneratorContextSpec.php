<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistProductGeneratorContext;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class WishlistProductGeneratorContextSpec extends ObjectBehavior
{
    private const QUANTITY = 100;

    private const STRESS = 15;

    public function let(
        SymfonyStyle $io,
        ChannelInterface $channel,
    ): void {
        $this->beConstructedWith(
            $io,
            self::QUANTITY,
            $channel,
            self::STRESS,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProductGeneratorContext::class);
    }

    public function it_returns_io(SymfonyStyle $io): void
    {
        $this->getIo()->shouldReturn($io);
    }

    public function it_returns_quantity(): void
    {
        $this->getQuantity()->shouldReturn(self::QUANTITY);
    }

    public function it_returns_channel(ChannelInterface $channel): void
    {
        $this->getChannel()->shouldReturn($channel);
    }

    public function it_returns_stress(): void
    {
        $this->getStress()->shouldReturn(self::STRESS);
    }
}
