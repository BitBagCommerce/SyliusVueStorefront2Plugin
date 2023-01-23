<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\AddItemToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class AddItemToWishlistHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;
    private WishlistProductFactoryInterface $wishlistProductFactory;
    private IriConverterInterface $iriConverter;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        IriConverterInterface $iriConverter
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->iriConverter = $iriConverter;
    }

    public function __invoke(AddItemToWishlist $command): WishlistInterface
    {
        /** @var WishlistInterface|null $wishlist */
        $wishlist = $this->iriConverter->getItemFromIri($command->getId());
        Assert::notNull($wishlist);

        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->iriConverter->getItemFromIri($command->getProductVariant());
        Assert::notNull($productVariant);

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant);

        $hasProductVariant = $wishlist->hasProductVariant($productVariant);
        Assert::false($hasProductVariant, 'There is this product variant');

        $wishlist->addWishlistProduct($wishlistProduct);
        $this->wishlistRepository->add($wishlist);

        return $wishlist;
    }
}
