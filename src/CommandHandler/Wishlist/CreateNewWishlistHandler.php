<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;


use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Checker\WishlistNameCheckerInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
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

    public function __construct(TokenStorageInterface $tokenStorage, WishlistRepositoryInterface $wishlistRepository, WishlistFactoryInterface $wishlistFactory, WishlistNameCheckerInterface $wishlistNameChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistNameChecker = $wishlistNameChecker;
    }


    public function __invoke(CreateNewWishlist $createNewWishlist)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        Assert::notEmpty($createNewWishlist->getName());

        $wishlist = $this->wishlistFactory->createForUser($user);
        $wishlist->setName($createNewWishlist->getName());

        $this->wishlistRepository->add($wishlist);

        return $wishlist;
    }
}
