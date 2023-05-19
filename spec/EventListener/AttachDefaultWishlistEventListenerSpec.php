<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\EventListener;

use BitBag\SyliusVueStorefront2Plugin\Model\ShopUserTokenInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class AttachDefaultWishlistEventListenerSpec extends ObjectBehavior
{
    function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelContextInterface $channelContext
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistFactory,
            $wishlistCookieTokenResolver,
            $channelContext
        );
    }

    function it_early_returns_when_no_user_is_set_to_a_token(
        ShopUserTokenInterface $shopUserToken
    ): void {
        $shopUserToken->getUser()->willReturn(null);

        $event = new GenericEvent($shopUserToken->getWrappedObject(), []);
        $this->addDefaultWishlist($event);
    }

    function it_early_returns_when_user_already_has_a_wishlist(
        ShopUserTokenInterface $shopUserToken,
        ShopUserInterface $shopUser,
        ChannelContextInterface $channelContext,
        ChannelInterface $currentChannel,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist
    ): void {
        $shopUserToken->getUser()->willReturn($shopUser);
        $channelContext->getChannel()->willReturn($currentChannel);

        $wishlistRepository->findOneByShopUserAndChannel($shopUser, $currentChannel)
            ->willReturn($wishlist);

        $event = new GenericEvent($shopUserToken->getWrappedObject(), []);
        $this->addDefaultWishlist($event);
    }

    function it_adds_default_wishlist(
        ShopUserTokenInterface $shopUserToken,
        ShopUserInterface $shopUser,
        ChannelContextInterface $channelContext,
        ChannelInterface $currentChannel,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
    ): void {
        $shopUserToken->getUser()->willReturn($shopUser);
        $channelContext->getChannel()->willReturn($currentChannel);

        $wishlistRepository->findOneByShopUserAndChannel($shopUser, $currentChannel)
            ->willReturn(null);

        $wishlistFactory->createForUserAndChannel($shopUser, $currentChannel)
            ->willReturn($wishlist);

        $wishlistToken = 'wishlist-token';
        $wishlistCookieTokenResolver->resolve()
            ->willReturn($wishlistToken);

        $wishlist->setName('My Wishlist')
            ->shouldBeCalledOnce();

        $wishlist->setToken($wishlistToken)
            ->shouldBeCalledOnce();

        $wishlistRepository->add($wishlist)
            ->shouldBeCalledOnce();

        $event = new GenericEvent($shopUserToken->getWrappedObject(), []);
        $this->addDefaultWishlist($event);
    }
}
