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
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductTaxonGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductWishlistGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\TaxonGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\UnknownBulkDataGeneratorException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\ProductBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\ProductWishlistBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\TaxonBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\WishlistBulkGeneratorInterface;

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
                ProductTaxonGeneratorContext::class => $this->productTaxonGeneratorContext($commandContext),
                ProductWishlistBulkGeneratorInterface::class => $this->productWishlistGeneratorContext($commandContext),
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
    ): ProductGeneratorContext {
        return new ProductGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getProductsQty(),
            $commandContext->getChannel(),
        );
    }

    private function taxonGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): TaxonGeneratorContext {
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
    ): ProductTaxonGeneratorContext {
        return new ProductTaxonGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getProductsPerTaxonQty(),
            $commandContext->getChannel(),
        );
    }

    private function productWishlistGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): ProductWishlistGeneratorContext {
        return new ProductWishlistGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getProductsPerWishlistQty(),
            $commandContext->getChannel(),
        );
    }
}
