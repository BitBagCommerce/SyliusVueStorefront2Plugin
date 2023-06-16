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
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\ProductBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\TaxonBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\WishlistBulkGenerator;

class BulkGeneratorContextFactory implements BulkGeneratorContextFactoryInterface
{
    public function fromCommandContext(
        DataGeneratorCommandContextInterface $commandContext,
        BulkGeneratorInterface $bulkGenerator,
    ): BulkContextInterface {
        return match ($bulkGenerator::class) {
            ProductBulkGenerator::class => $this->productBulkGeneratorContext($commandContext),
            TaxonBulkGenerator::class => $this->taxonBulkGeneratorContext($commandContext),
            WishlistBulkGenerator::class => $this->wishlistBulkGeneratorContext($commandContext),
            default => throw new UnknownBulkDataGeneratorException(),
        };
    }

    private function productBulkGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): BulkContextInterface {
        return new BulkContext(
            $commandContext->getWishlistsQty(),
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
