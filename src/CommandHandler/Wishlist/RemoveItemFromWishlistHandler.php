<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\RemoveItemFromWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class RemoveItemFromWishlistHandler implements MessageHandlerInterface
{
    private ObjectManager $wishlistManager;
    private IriConverterInterface $iriConverter;

    public function __construct(
        ObjectManager $wishlistManager,
        IriConverterInterface $iriConverter
    ) {
        $this->wishlistManager = $wishlistManager;
        $this->iriConverter = $iriConverter;
    }

    public function __invoke(RemoveItemFromWishlist $command): WishlistInterface
    {
        /** @var WishlistInterface|null $wishlist */
        $wishlist = $this->iriConverter->getItemFromIri($command->getId());
        Assert::notNull($wishlist);

        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->iriConverter->getItemFromIri($command->getProductVariant());
        Assert::notNull($productVariant);

        $wishlist->removeProductVariant($productVariant);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
