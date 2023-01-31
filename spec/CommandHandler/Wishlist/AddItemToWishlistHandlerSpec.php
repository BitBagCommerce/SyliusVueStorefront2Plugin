<?php

namespace spec\BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\AddItemToWishlist;
use BitBag\SyliusVueStorefront2Plugin\CommandHandler\Wishlist\AddItemToWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\InvalidArgumentException;

class AddItemToWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        IriConverterInterface $iriConverter,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistProductFactory,
            $iriConverter,
            $eventDispatcher
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddItemToWishlistHandler::class);
    }

    public function it_is_invokable_for_a_new_product_variant(
        IriConverterInterface $iriConverter,
        WishlistInterface $wishlist,
        ProductVariantInterface $productVariant,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        WishlistRepositoryInterface $wishlistRepository,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $addItemToWishlist = new AddItemToWishlist('wishlistIri', 'productVariantIri');

        $iriConverter->getItemFromIri($addItemToWishlist->getId())->willReturn($wishlist);
        $iriConverter->getItemFromIri($addItemToWishlist->getProductVariant())->willReturn($productVariant);

        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);

        $wishlist->hasProductVariant($productVariant)->willReturn(false);
        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();

        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), AddItemToWishlistHandler::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($addItemToWishlist)->shouldReturn($wishlist);
    }

    public function it_is_invokable_for_existing_product_variant_in_wishlist(
        IriConverterInterface $iriConverter,
        WishlistInterface $wishlist,
        ProductVariantInterface $productVariant,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
    ): void {
        $addItemToWishlist = new AddItemToWishlist('wishlistIri', 'productVariantIri');

        $iriConverter->getItemFromIri($addItemToWishlist->getId())->willReturn($wishlist);
        $iriConverter->getItemFromIri($addItemToWishlist->getProductVariant())->willReturn($productVariant);

        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);

        $wishlist->hasProductVariant($productVariant)->willReturn(true);
        $wishlist->addWishlistProduct($wishlistProduct)->shouldNotBeCalled();

        $this->__invoke($addItemToWishlist)->shouldReturn($wishlist);
    }

    public function it_throws_an_exception_when_cannot_find_wishlist(
        IriConverterInterface $iriConverter,
    ): void {
        $addItemToWishlist = new AddItemToWishlist('wishlistIri', 'productVariantIri');

        $iriConverter->getItemFromIri($addItemToWishlist->getId())->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addItemToWishlist]);
    }
    public function it_throws_an_exception_when_cannot_find_product_variant(
        IriConverterInterface $iriConverter,
        WishlistInterface $wishlist,
    ): void {
        $addItemToWishlist = new AddItemToWishlist('wishlistIri', 'productVariantIri');

        $iriConverter->getItemFromIri($addItemToWishlist->getId())->willReturn($wishlist);
        $iriConverter->getItemFromIri($addItemToWishlist->getProductVariant())->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addItemToWishlist]);
    }
}
