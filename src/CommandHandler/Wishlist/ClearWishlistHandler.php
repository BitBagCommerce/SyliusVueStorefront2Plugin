<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\ClearWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class ClearWishlistHandler  implements MessageHandlerInterface
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

    public function __invoke(ClearWishlist $command): WishlistInterface
    {
        /** @var WishlistInterface|null $wishlist */
        $wishlist = $this->iriConverter->getItemFromIri($command->getId());
        Assert::notNull($wishlist);

        $wishlist->clear();
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
