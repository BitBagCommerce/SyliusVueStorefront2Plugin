<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\AddItemToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class AddItemToWishlistHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag.sylius_vue_storefront2.add_item_to_wishlist.complete';

    private WishlistRepositoryInterface $wishlistRepository;
    private WishlistProductFactoryInterface $wishlistProductFactory;
    private IriConverterInterface $iriConverter;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        IriConverterInterface $iriConverter,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->iriConverter = $iriConverter;
        $this->eventDispatcher = $eventDispatcher;
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

        if ($wishlist->hasProductVariant($productVariant)) {
            return $wishlist;
        }

        $wishlist->addWishlistProduct($wishlistProduct);
        $this->wishlistRepository->add($wishlist);

        $this->eventDispatcher->dispatch(new GenericEvent($wishlist, [$command]), self::EVENT_NAME);

        return $wishlist;
    }
}
