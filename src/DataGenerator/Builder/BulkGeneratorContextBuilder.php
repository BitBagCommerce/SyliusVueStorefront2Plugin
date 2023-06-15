<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Builder;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext\BulkContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext\BulkContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\ProductContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\TaxonContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\WishlistContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\ProductBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\TaxonBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\WishlistBulkGenerator;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BulkGeneratorContextBuilder implements BulkGeneratorContextBuilderInterface
{
    public static function buildFromCommandContext(
        DataGeneratorCommandContextInterface $commandContext,
        BulkGeneratorInterface $bulkGenerator,
    ): BulkContextInterface {
        return match ($bulkGenerator::class) {
            ProductBulkGenerator::class => self::productBulkGeneratorContext(
                $commandContext->getProductsQty(),
                $commandContext->getIO(),
                $commandContext->getChannel(),
            ),
            TaxonBulkGenerator::class => self::taxonBulkGeneratorContext(
                $commandContext->getTaxonsQty(),
                $commandContext->getIO(),
                $commandContext->getMaxTaxonLevel(),
                $commandContext->getMaxChildrenPerTaxonLevel(),
            ),
            WishlistBulkGenerator::class => self::wishlistBulkGeneratorContext(
                $commandContext->getWishlistsQty(),
                $commandContext->getIO(),
                $commandContext->getChannel(),
            ),
        };
    }

    private static function productBulkGeneratorContext(
        int $quantity,
        SymfonyStyle $io,
        ChannelInterface $channel,
    ): BulkContextInterface {
        return new BulkContext($quantity, $io, new ProductContext($channel));
    }

    private static function taxonBulkGeneratorContext(
        int $quantity,
        SymfonyStyle $io,
        int $maxTaxonLevel,
        int $maxChildrenPerTaxonLevel,
    ): BulkContextInterface {
        return new BulkContext(
            $quantity,
            $io,
            new TaxonContext($maxTaxonLevel, $maxChildrenPerTaxonLevel)
        );
    }

    private static function wishlistBulkGeneratorContext(
        int $quantity,
        SymfonyStyle $io,
        ChannelInterface $channel,
    ): BulkContextInterface {
        return new BulkContext($quantity, $io, new WishlistContext($channel));
    }
}
