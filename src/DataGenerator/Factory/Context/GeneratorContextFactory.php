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
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\TaxonGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistGeneratorContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\UnknownBulkDataGeneratorException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\ProductBulkGeneratorInterface;
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
                ProductBulkGeneratorInterface::class => $this->productBulkGeneratorContext($commandContext),
                TaxonBulkGeneratorInterface::class => $this->taxonBulkGeneratorContext($commandContext),
                WishlistBulkGeneratorInterface::class => $this->wishlistBulkGeneratorContext($commandContext),
                default => null,
            };

            if ($context instanceof GeneratorContextInterface) {
                return $context;
            }
        }

        throw new UnknownBulkDataGeneratorException();
    }

    private function productBulkGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): GeneratorContextInterface {
        return new ProductGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getProductsQty(),
            $commandContext->getChannel(),
        );
    }

    private function taxonBulkGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): GeneratorContextInterface {
        return new TaxonGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getTaxonsQty(),
            $commandContext->getMaxTaxonLevel(),
            $commandContext->getMaxChildrenPerTaxonLevel(),
        );
    }

    private function wishlistBulkGeneratorContext(
        DataGeneratorCommandContextInterface $commandContext,
    ): GeneratorContextInterface {
        return new WishlistGeneratorContext(
            $commandContext->getIO(),
            $commandContext->getWishlistsQty(),
            $commandContext->getChannel(),
        );
    }
}
