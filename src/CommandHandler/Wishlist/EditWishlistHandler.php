<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\EditWishlist;
use BitBag\SyliusWishlistPlugin\Checker\WishlistNameCheckerInterface;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class EditWishlistHandler implements MessageHandlerInterface
{

    private WishlistRepositoryInterface $wishlistRepository;
    private WishlistNameCheckerInterface $wishlistNameChecker;
    private IriConverterInterface $iriConverter;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistNameCheckerInterface $wishlistNameChecker,
        IriConverterInterface $iriConverter
    )
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistNameChecker = $wishlistNameChecker;
        $this->iriConverter = $iriConverter;
    }

    public function __invoke(EditWishlist $command)
    {
        /** @var WishlistInterface|null $wishlist */
        $wishlist = $this->iriConverter->getItemFromIri($command->getId());
        Assert::notNull($wishlist);
        Assert::notEmpty($command->getName());

        $wishlists = $this->wishlistRepository->findAllByShopUser($wishlist->getShopUser()->getId());

        foreach ($wishlists as $newWishlist) {
            $isSameName = $this->wishlistNameChecker->check($newWishlist->getName(), $command->getName());
            Assert::false($isSameName, "Name is the same");
        }

        $wishlist->setName($command->getName());
        $this->wishlistRepository->add($wishlist);

        return $wishlist;
    }
}
