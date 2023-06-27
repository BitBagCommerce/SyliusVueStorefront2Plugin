<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\ProductRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection\WishlistProductCollectionGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\SimpleType\Integer\IntegerGenerator;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class WishlistProductCollectionGeneratorSpec extends ObjectBehavior
{
    public function let(
        ProductRepositoryInterface $productRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        EntityManagerInterface $entityManager,
        IntegerGenerator $integerGenerator,
    ): void {
        $this->beConstructedWith($productRepository, $wishlistProductFactory, $entityManager, $integerGenerator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProductCollectionGenerator::class);
    }

    public function it_generates_wishlist_product_collection(
        ProductRepositoryInterface $productRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        IntegerGenerator $integerGenerator,
        ChannelInterface $channel,
        WishlistInterface $wishlist,
        WishlistProductGeneratorContextInterface $context,
        ProductInterface $product1,
        ProductInterface $product2,
        WishlistProductInterface $wishlistProduct1,
        WishlistProductInterface $wishlistProduct2,
    ): void {
        $quantity = 10;
        $stress = 20;
        $products = [$product1, $product2];
        $productsCount = count($products);
        $min = 0;
        $topValuesThreshold = 80;
        $randomInt = 2;
        $limit = 100;
        $offset = 0;
        $wishlistProducts = [$wishlistProduct1, $wishlistProduct2];

        $context->getChannel()->willReturn($channel);
        $context->getQuantity()->willReturn($quantity);
        $context->getStress()->willReturn($stress);

        $productRepository->getEntityCount($channel)->willReturn($productsCount);
        $integerGenerator->generateBiased($min, $productsCount, $stress, $topValuesThreshold)->willReturn($randomInt);

        $productRepository->findByChannel($channel, $limit, $offset)->willReturn($products);

        $i = 0;
        foreach ($products as $product) {
            $wishlist->hasProduct($product)->willReturn(false);
            $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->willReturn($wishlistProducts[$i]);
            $wishlist->addWishlistProduct($wishlistProducts[$i])->shouldBeCalled();

            $i++;
        }

        $productRepository->findByChannel($channel, $limit, $offset + $limit)->willReturn([]);

        $this->generate($wishlist->getWrappedObject(), $context);
    }

    public function it_does_nothing_if_no_products_found(
        ProductRepositoryInterface $productRepository,
        ChannelInterface $channel,
        WishlistInterface $wishlist,
        WishlistProductGeneratorContextInterface $context,
    ): void {
        $productsCount = 0;
        $quantity = 10;
        $stress = 20;

        $context->getChannel()->willReturn($channel);
        $context->getQuantity()->willReturn($quantity);
        $context->getStress()->willReturn($stress);

        $productRepository->getEntityCount($channel)->willReturn($productsCount);

        $this->generate($wishlist->getWrappedObject(), $context);
    }

    public function it_does_nothing_if_wishlist_product_exists(
        ProductRepositoryInterface $productRepository,
        IntegerGenerator $integerGenerator,
        ChannelInterface $channel,
        WishlistInterface $wishlist,
        WishlistProductGeneratorContextInterface $context,
        ProductInterface $product1,
        ProductInterface $product2,
    ): void {
        $products = [$product1, $product2];
        $productsCount = count($products);
        $min = 0;
        $quantity = 10;
        $stress = 20;
        $topValuesThreshold = 80;
        $randomInt = 2;
        $limit = 100;
        $offset = 0;

        $context->getChannel()->willReturn($channel);
        $context->getQuantity()->willReturn($quantity);
        $context->getStress()->willReturn($stress);

        $productRepository->getEntityCount($channel)->willReturn($productsCount);
        $integerGenerator->generateBiased($min, $productsCount, $stress, $topValuesThreshold)->willReturn($randomInt);

        $productRepository->findByChannel($channel, $limit, $offset)->willReturn($products);

        foreach ($products as $product) {
            $wishlist->hasProduct($product)->willReturn(true);
        }

        $productRepository->findByChannel($channel, $limit, $offset + $limit)->willReturn([]);

        $this->generate($wishlist->getWrappedObject(), $context);
    }

    public function it_throws_exception_on_invalid_context(
        WishlistInterface $wishlist,
        ContextInterface $context,
    ): void {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$wishlist, $context]);
    }
}
