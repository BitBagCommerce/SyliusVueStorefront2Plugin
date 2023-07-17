<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\EventListener;

use BitBag\SyliusVueStorefront2Plugin\Model\ShopUserTokenInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class AttachDefaultWishlistEventListener
{
    private const DEFAULT_WISHLIST_NAME = 'My Wishlist';

    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private WishlistFactoryInterface $wishlistFactory,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        private ChannelContextInterface $channelContext,
    ) {

    }

    public function addDefaultWishlist(GenericEvent $event): void
    {
        /** @var ShopUserTokenInterface $shopUserToken */
        $shopUserToken = $event->getSubject();
        $shopUser = $shopUserToken->getUser();

        if ($shopUser === null) {
            return;
        }

        /** @var ChannelInterface $currentChannel */
        $currentChannel = $this->channelContext->getChannel();

        $wishlist = $this->wishlistRepository->findOneByShopUserAndChannel($shopUser, $currentChannel);
        if ($wishlist !== null) {
            return;
        }

        $wishlist = $this->wishlistFactory->createForUserAndChannel($shopUser, $currentChannel);
        $wishlist->setName(self::DEFAULT_WISHLIST_NAME);

        $wishlistToken = $this->wishlistCookieTokenResolver->resolve();
        $wishlist->setToken($wishlistToken);

        $this->wishlistRepository->add($wishlist);
    }
}
