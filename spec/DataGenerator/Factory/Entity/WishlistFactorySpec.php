<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\WishlistFactory;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use PhpSpec\ObjectBehavior;
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

    public function it_creates(
        FactoryInterface $wishlistFactory,
        ChannelInterface $channel,
        ShopUserInterface $shopUser,
    ): void {
        $name = 'My test wishlist';
        $token = md5('my test token to for my test wishlist');

        $wishlist = new Wishlist();

        $wishlistFactory->createNew()->willReturn($wishlist);
        $wishlist->setName($name);
        $wishlist->setToken($token);
        $wishlist->setChannel($channel->getWrappedObject());
        $wishlist->setShopUser($shopUser->getWrappedObject());

        $this->create($name, $token, $channel, $shopUser);
    }
}
