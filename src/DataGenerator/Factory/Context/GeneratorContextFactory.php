<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\GeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductTaxonGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductTaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\TaxonGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\TaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistProductGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\UnknownBulkDataGeneratorException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Collection\ProductTaxonCollectionBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Collection\WishlistProductCollectionBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Entity\ProductBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Entity\TaxonBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Entity\WishlistBulkGeneratorInterface;

final class GeneratorContextFactory implements GeneratorContextFactoryInterface
{
    public function fromCommandContext(
        DataGeneratorCommandContextInterface $commandContext,
        BulkGeneratorInterface $bulkGenerator,
    ): GeneratorContextInterface {
        $interfaces = class_implements($bulkGenerator);
        $interfaces = is_array($interfaces) ? $interfaces : [$interfaces];
        foreach ($interfaces as $interface) {
            $context = match ($interface) {
                ProductBulkGeneratorInterface::class => $this->productGeneratorContext($commandContext),
                TaxonBulkGeneratorInterface::class => $this->taxonGeneratorContext($commandContext),
                WishlistBulkGeneratorInterface::class => $this->wishlistGeneratorContext($commandContext),
                ProductTaxonCollectionBulkGeneratorInterface::class => $this->productTaxonGeneratorContext($commandContext),
                WishlistProductCollectionBulkGeneratorInterface::class => $this->wishlistProductGeneratorContext($commandContext),
                default => null,
            };

            if ($context instanceof GeneratorContextInterface) {
                return $context;
            }
        }

        throw new UnknownBulkDataGeneratorException();
    }

    private function productGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): ProductGeneratorContextInterface {
        return new ProductGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getProductsQty(),
            $commandContext->getChannel(),
        );
    }

    private function taxonGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): TaxonGeneratorContextInterface {
        return new TaxonGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getTaxonsQty(),
            $commandContext->getMaxTaxonLevel(),
            $commandContext->getMaxChildrenPerTaxonLevel(),
        );
    }

    private function wishlistGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): WishlistGeneratorContext {
        return new WishlistGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getWishlistsQty(),
            $commandContext->getChannel(),
        );
    }

    private function productTaxonGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): ProductTaxonGeneratorContextInterface {
        return new ProductTaxonGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getProductsPerTaxonQty(),
            $commandContext->getChannel(),
            $commandContext->getStress(),
        );
    }

    private function wishlistProductGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): WishlistProductGeneratorContextInterface {
        return new WishlistProductGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getProductsPerWishlistQty(),
            $commandContext->getChannel(),
            $commandContext->getStress(),
        );
    }
}
