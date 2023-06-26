<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Bulk\BulkGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\UnknownBulkDataGeneratorException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context\BulkGeneratorContextFactory;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\ProductBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\TaxonBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\WishlistBulkGeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class BulkGeneratorContextFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BulkGeneratorContextFactory::class);
    }

    function it_returns_product_bulk_generator_context(
        DataGeneratorCommandContextInterface $commandContext,
        ProductBulkGeneratorInterface $bulkGenerator,
        SymfonyStyle $io,
        ChannelInterface $channel,
    ): void {
        $quantity = 100;

        $commandContext->getProductsQty()->willReturn($quantity);
        $commandContext->getIO()->willReturn($io->getWrappedObject());
        $commandContext->getChannel()->willReturn($channel->getWrappedObject());

        $this->fromCommandContext($commandContext->getWrappedObject(), $bulkGenerator->getWrappedObject())
            ->shouldReturnAnInstanceOf(BulkGeneratorContextInterface::class);
    }

    function it_returns_taxon_bulk_generator_context(
        DataGeneratorCommandContextInterface $commandContext,
        TaxonBulkGeneratorInterface $bulkGenerator,
        SymfonyStyle $io,
    ): void {
        $quantity = 100;
        $maxTaxonLevel = 100;
        $maxChildrenPerTaxonLevel = 100;

        $commandContext->getTaxonsQty()->willReturn($quantity);
        $commandContext->getIO()->willReturn($io->getWrappedObject());
        $commandContext->getMaxTaxonLevel()->willReturn($maxTaxonLevel);
        $commandContext->getMaxChildrenPerTaxonLevel()->willReturn($maxChildrenPerTaxonLevel);

        $this->fromCommandContext($commandContext->getWrappedObject(), $bulkGenerator->getWrappedObject())
            ->shouldReturnAnInstanceOf(BulkGeneratorContextInterface::class);
    }

    function it_returns_wishlist_bulk_generator_context(
        DataGeneratorCommandContextInterface $commandContext,
        WishlistBulkGeneratorInterface $bulkGenerator,
        SymfonyStyle $io,
        ChannelInterface $channel,
    ): void {
        $quantity = 100;

        $commandContext->getWishlistsQty()->willReturn($quantity);
        $commandContext->getIO()->willReturn($io->getWrappedObject());
        $commandContext->getChannel()->willReturn($channel->getWrappedObject());

        $this->fromCommandContext($commandContext->getWrappedObject(), $bulkGenerator->getWrappedObject())
            ->shouldReturnAnInstanceOf(BulkGeneratorContextInterface::class);
    }

    function it_throws_exception_when_bulk_generator_is_unknown(
        DataGeneratorCommandContextInterface $commandContext,
        BulkGeneratorInterface $bulkGenerator
    ): void {
        $this->shouldThrow(UnknownBulkDataGeneratorException::class)->during('fromCommandContext', [$commandContext, $bulkGenerator]);
    }
}