<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\WishlistFactory;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $wishlistFactory): void
    {
        $this->beConstructedWith($wishlistFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistFactory::class);
    }

    public function it_creates_wishlist(
        FactoryInterface $wishlistFactory,
        ChannelInterface $channel,
        ShopUserInterface $shopUser,
        WishlistInterface $wishlist,
    ): void {
        $wishlistFactory->createNew()->willReturn($wishlist);

        $this
            ->create(
                Argument::type('string'),
                Argument::type('string'),
                $channel,
                $shopUser
            )
            ->shouldReturn($wishlist);
    }
}
