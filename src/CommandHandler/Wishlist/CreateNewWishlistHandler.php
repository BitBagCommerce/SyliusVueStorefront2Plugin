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
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class CreateNewWishlistHandler implements MessageHandlerInterface
{
    private TokenStorageInterface $tokenStorage;

    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistFactoryInterface $wishlistFactory;

    private WishlistNameCheckerInterface $wishlistNameChecker;
    private ChannelRepositoryInterface $channelRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistNameCheckerInterface $wishlistNameChecker,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistNameChecker = $wishlistNameChecker;
        $this->channelRepository = $channelRepository;
    }

    public function __invoke(CreateNewWishlist $createNewWishlist): WishlistInterface
    {
        $user = $this->tokenStorage->getToken()->getUser();

        Assert::notEmpty($createNewWishlist->getName());
        Assert::notEmpty($createNewWishlist->getChannelCode());
        Assert::isInstanceOf($user, ShopUserInterface::class);

        $channel = $this->channelRepository->findOneByCode($createNewWishlist->getChannelCode());

        Assert::isInstanceOf($channel, Channel::class);

        $wishlists = $this->wishlistRepository->findAllByShopUser($user->getId());

        $wishlist = $this->wishlistFactory->createForUserAndChannel($user, $channel);
        $wishlist->setName($createNewWishlist->getName());

        foreach ($wishlists as $newWishlist) {
            $isSameName = $this->wishlistNameChecker->check($newWishlist->getName(), $wishlist->getName());
            Assert::false($isSameName, "Name is the same");
        }

        $this->wishlistRepository->add($wishlist);

        return $wishlist;
    }
}
