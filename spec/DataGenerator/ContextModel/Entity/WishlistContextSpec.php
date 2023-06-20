<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\WishlistContext;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;

final class WishlistContextSpec extends ObjectBehavior
{
    public function let(ChannelInterface $channel): void
    {
        $this->beConstructedWith($channel);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistContext::class);
    }

    public function it_returns_channel(ChannelInterface $channel): void
    {
        $this->getChannel()->shouldReturn($channel);
    }
}
