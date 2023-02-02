<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;

use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist\CreateNewWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;
use Webmozart\Assert\InvalidArgumentException;

final class CreateNewWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        Security $security,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ChannelRepositoryInterface $channelRepository,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $this->beConstructedWith(
            $security,
            $wishlistRepository,
            $wishlistFactory,
            $channelRepository,
            $eventDispatcher,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateNewWishlistHandler::class);
    }

    public function it_is_invokable_for_first_wishlist(
        Security $security,
        ShopUserInterface $shopUser,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist,
        WishlistRepositoryInterface $wishlistRepository,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $createNewWishlist = new CreateNewWishlist('Wishlist', 'channelCode');

        $security->getUser()->willReturn($shopUser);
        $channelRepository->findOneByCode($createNewWishlist->getChannelCode())->willReturn($channel);

        $wishlistFactory->createForUserAndChannel($shopUser, $channel)->willReturn($wishlist);
        $wishlist->setName($createNewWishlist->getName())->shouldBeCalled();

        $wishlistRepository->findOneByShopUser($shopUser)->willReturn(null);
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), CreateNewWishlistHandler::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($createNewWishlist)->shouldReturn($wishlist);
    }

    public function it_is_invokable_for_next_wishlist(
        Security $security,
        ShopUserInterface $shopUser,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        WishlistFactoryInterface $wishlistFactory,
        WishlistInterface $wishlist,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $oldWishlist,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $createNewWishlist = new CreateNewWishlist('Wishlist', 'channelCode');
        $token = 'Token';

        $security->getUser()->willReturn($shopUser);
        $channelRepository->findOneByCode($createNewWishlist->getChannelCode())->willReturn($channel);

        $wishlistFactory->createForUserAndChannel($shopUser, $channel)->willReturn($wishlist);
        $wishlist->setName($createNewWishlist->getName())->shouldBeCalled();

        $wishlistRepository->findOneByShopUser($shopUser)->willReturn($oldWishlist);
        $oldWishlist->getToken()->willReturn($token);
        $wishlist->setToken($token)->shouldBeCalled();

        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), CreateNewWishlistHandler::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($createNewWishlist)->shouldReturn($wishlist);
    }

    public function it_throws_an_exception_for_anonymous_user(Security $security): void
    {
        $createNewWishlist = new CreateNewWishlist('Wishlist', 'channelCode');
        $security->getUser()->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$createNewWishlist])
        ;
    }

    public function it_throws_an_exception_when_cannot_find_channel(
        Security $security,
        ShopUserInterface $shopUser,
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $createNewWishlist = new CreateNewWishlist('Wishlist', 'channelCode');

        $security->getUser()->willReturn($shopUser);
        $channelRepository->findOneByCode($createNewWishlist->getChannelCode())->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$createNewWishlist])
        ;
    }
}
