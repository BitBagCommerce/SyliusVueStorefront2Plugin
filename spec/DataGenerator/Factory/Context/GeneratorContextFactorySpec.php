<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductTaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\TaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\UnknownBulkDataGeneratorException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context\GeneratorContextFactory;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Collection\ProductTaxonCollectionBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Collection\WishlistProductCollectionBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Entity\ProductBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Entity\TaxonBulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Entity\WishlistBulkGeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GeneratorContextFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(GeneratorContextFactory::class);
    }

    function it_returns_product_generator_context(
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
            ->shouldReturnAnInstanceOf(ProductGeneratorContextInterface::class);
    }

    function it_returns_taxon_generator_context(
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
            ->shouldReturnAnInstanceOf(TaxonGeneratorContextInterface::class);
    }

    function it_returns_wishlist_generator_context(
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
            ->shouldReturnAnInstanceOf(WishlistGeneratorContextInterface::class);
    }

    function it_returns_product_taxon_generator_context(
        DataGeneratorCommandContextInterface $commandContext,
        ProductTaxonCollectionBulkGeneratorInterface $bulkGenerator,
        SymfonyStyle $io,
        ChannelInterface $channel,
    ): void {
        $quantity = 100;
        $stress = 40;

        $commandContext->getIO()->willReturn($io->getWrappedObject());
        $commandContext->getProductsPerTaxonQty()->willReturn($quantity);
        $commandContext->getChannel()->willReturn($channel->getWrappedObject());
        $commandContext->getStress()->willReturn($stress);

        $this->fromCommandContext($commandContext->getWrappedObject(), $bulkGenerator->getWrappedObject())
            ->shouldReturnAnInstanceOf(ProductTaxonGeneratorContextInterface::class);
    }

    function it_returns_wishlist_product_generator_context(
        DataGeneratorCommandContextInterface $commandContext,
        WishlistProductCollectionBulkGeneratorInterface $bulkGenerator,
        SymfonyStyle $io,
        ChannelInterface $channel,
    ): void {
        $quantity = 100;
        $stress = 40;

        $commandContext->getIO()->willReturn($io->getWrappedObject());
        $commandContext->getProductsPerWishlistQty()->willReturn($quantity);
        $commandContext->getChannel()->willReturn($channel->getWrappedObject());
        $commandContext->getStress()->willReturn($stress);

        $this->fromCommandContext($commandContext->getWrappedObject(), $bulkGenerator->getWrappedObject())
            ->shouldReturnAnInstanceOf(WishlistProductGeneratorContextInterface::class);
    }

    function it_throws_exception_when_generator_is_unknown(
        DataGeneratorCommandContextInterface $commandContext,
        BulkGeneratorInterface $bulkGenerator
    ): void {
        $this->shouldThrow(UnknownBulkDataGeneratorException::class)->during('fromCommandContext', [$commandContext, $bulkGenerator]);
    }
}
