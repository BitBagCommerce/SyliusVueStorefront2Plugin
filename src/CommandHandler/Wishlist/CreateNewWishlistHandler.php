<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;


use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Checker\WishlistNameCheckerInterface;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Webmozart\Assert\Assert;

final class CreateNewWishlistHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag.sylius_vue_storefront2.create_new_wishlist.complete';

    private Security $security;

    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistFactoryInterface $wishlistFactory;

    private ChannelRepositoryInterface $channelRepository;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Security $security,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ChannelRepositoryInterface $channelRepository,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->security = $security;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->channelRepository = $channelRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateNewWishlist $command): WishlistInterface
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->security->getUser();
        Assert::isInstanceOf($user, ShopUserInterface::class);

        $channel = $this->channelRepository->findOneByCode($command->getChannelCode());
        Assert::isInstanceOf($channel, Channel::class);

        $wishlist = $this->wishlistFactory->createForUserAndChannel($user, $channel);
        $wishlist->setName($command->getName());

        if (null !== $oldWishlist = $this->wishlistRepository->findOneByShopUser($user)) {
            $wishlist->setToken($oldWishlist->getToken());
        }

        $this->wishlistRepository->add($wishlist);

        $this->eventDispatcher->dispatch(new GenericEvent($wishlist, [$command]), self::EVENT_NAME);

        return $wishlist;
    }
}
