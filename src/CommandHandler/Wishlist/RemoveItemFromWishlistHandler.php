<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\RemoveItemFromWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class RemoveItemFromWishlistHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag.sylius_vue_storefront2.remove_item_from_wishlist.complete';

    private ObjectManager $wishlistManager;
    private IriConverterInterface $iriConverter;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ObjectManager $wishlistManager,
        IriConverterInterface $iriConverter,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->wishlistManager = $wishlistManager;
        $this->iriConverter = $iriConverter;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(RemoveItemFromWishlist $command): WishlistInterface
    {
        /** @var WishlistInterface|null $wishlist */
        $wishlist = $this->iriConverter->getItemFromIri($command->getId());
        Assert::notNull($wishlist);

        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->iriConverter->getItemFromIri($command->getProductVariant());
        Assert::notNull($productVariant);


        if ($wishlist->hasProductVariant($productVariant)) {
            return $wishlist;
        }

        $wishlist->removeProductVariant($productVariant);
        $this->wishlistManager->flush();

        $this->eventDispatcher->dispatch(new GenericEvent($wishlist, [$command]), self::EVENT_NAME);

        return $wishlist;
    }
}
