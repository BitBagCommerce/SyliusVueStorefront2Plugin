<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext\BulkContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext\BulkContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\ProductContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\TaxonContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\WishlistContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\UnknownBulkDataGeneratorException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\ProductBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\TaxonBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\WishlistBulkGeneratorInterface;

class BulkGeneratorContextFactory implements BulkGeneratorContextFactoryInterface
{
    public function fromCommandContext(
        DataGeneratorCommandContextInterface $commandContext,
        BulkGeneratorInterface $bulkGenerator,
    ): BulkContextInterface {
        foreach (class_implements($bulkGenerator) as $interface) {
            $context = match ($interface) {
                ProductBulkGeneratorInterface::class => $this->productBulkGeneratorContext($commandContext),
                TaxonBulkGeneratorInterface::class => $this->taxonBulkGeneratorContext($commandContext),
                WishlistBulkGeneratorInterface::class => $this->wishlistBulkGeneratorContext($commandContext),
                default => null,
            };

            if ($context instanceof BulkContextInterface) {
                return $context;
            }
        }

        throw new UnknownBulkDataGeneratorException();
    }

    private function productBulkGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): BulkContextInterface {
        return new BulkContext(
            $commandContext->getProductsQty(),
            $commandContext->getIO(),
            new ProductContext($commandContext->getChannel()),
        );
    }

    private function taxonBulkGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): BulkContextInterface {
        return new BulkContext(
            $commandContext->getTaxonsQty(),
            $commandContext->getIO(),
            new TaxonContext(
                $commandContext->getMaxTaxonLevel(),
                $commandContext->getMaxChildrenPerTaxonLevel(),
            )
        );
    }

    private function wishlistBulkGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): BulkContextInterface {
        return new BulkContext(
            $commandContext->getWishlistsQty(),
            $commandContext->getIO(),
            new WishlistContext($commandContext->getChannel()),
        );
    }
}
