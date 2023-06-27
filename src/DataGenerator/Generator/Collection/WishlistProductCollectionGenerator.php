<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\ProductRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\SimpleType\Integer\IntegerGenerator;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class WishlistProductCollectionGenerator implements WishlistProductCollectionGeneratorInterface
{
    private ProductRepositoryInterface $productRepository;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private EntityManagerInterface $entityManager;

    private IntegerGenerator $integerGenerator;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        EntityManagerInterface $entityManager,
        IntegerGenerator $integerGenerator,
    ) {
        $this->productRepository = $productRepository;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->entityManager = $entityManager;
        $this->integerGenerator = $integerGenerator;
    }

    public function generate(
        WishlistInterface $wishlist,
        ContextInterface $context,
    ): void {
        if (!$context instanceof WishlistProductGeneratorContextInterface) {
            throw new InvalidContextException();
        }

        $channel = $context->getChannel();
        $productsCount = $this->productRepository->getEntityCount($channel);
        if ($productsCount === 0) {
            return;
        }

        $randomInt = $this->integerGenerator->generateBiased(
            0,
            min($productsCount, $context->getQuantity()),
            $context->getStress(),
            self::TOP_VALUES_THRESHOLD,
        );

        $maxOffset = max(0, $productsCount - $randomInt);
        $offset = mt_rand(0, $maxOffset);
        $i = 0;

        while (
            count($products = $this->productRepository->findByChannel($channel, self::LIMIT, $offset)) > 0
        ) {
            foreach ($products as $product) {
                if ($wishlist->hasProduct($product)) {
                    continue;
                }
                $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);
                $wishlist->addWishlistProduct($wishlistProduct);

                $this->entityManager->persist($wishlistProduct);

                $i++;
                if ($i % self::FLUSH_AFTER === 0) {
                    $this->entityManager->flush();
                }
            }

            $offset += self::LIMIT;

            $this->entityManager->flush();
        }
    }
}
