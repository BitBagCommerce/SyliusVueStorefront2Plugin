<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContext;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DataGeneratorCommandContextSpec extends ObjectBehavior
{
    private const PRODUCTS_QTY = 100;

    private const TAXONS_QTY = 200;

    private const WISHLISTS_QTY = 300;

    private const PRODUCTS_PER_TAXON_QTY = 400;

    private const MAX_TAXON_LEVEL = 500;

    private const MAX_CHILDREN_PER_TAXON_LEVEL = 600;

    private const PRODUCTS_PER_WISHLIST_QTY = 700;

    private const STRESS = 25;

    public function let(
        SymfonyStyle $io,
        ChannelInterface $channel,
    ): void {
        $this->beConstructedWith(
            $io,
            $channel,
            self::PRODUCTS_QTY,
            self::TAXONS_QTY,
            self::WISHLISTS_QTY,
            self::PRODUCTS_PER_TAXON_QTY,
            self::MAX_TAXON_LEVEL,
            self::MAX_CHILDREN_PER_TAXON_LEVEL,
            self::PRODUCTS_PER_WISHLIST_QTY,
            self::STRESS,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DataGeneratorCommandContext::class);
    }

    public function it_returns_io(SymfonyStyle $io): void
    {
        $this->getIO()->shouldReturn($io);
    }

    public function it_returns_channel(ChannelInterface $channel): void
    {
        $this->getChannel()->shouldReturn($channel);
    }

    public function it_returns_products_qty(): void
    {
        $this->getProductsQty()->shouldReturn(self::PRODUCTS_QTY);
    }

    public function it_returns_taxons_qty(): void
    {
        $this->getTaxonsQty()->shouldReturn(self::TAXONS_QTY);
    }

    public function it_returns_wishlists_qty(): void
    {
        $this->getWishlistsQty()->shouldReturn(self::WISHLISTS_QTY);
    }

    public function it_returns_products_per_taxon_qty(): void
    {
        $this->getProductsPerTaxonQty()->shouldReturn(self::PRODUCTS_PER_TAXON_QTY);
    }

    public function it_returns_max_taxon_level(): void
    {
        $this->getMaxTaxonLevel()->shouldReturn(self::MAX_TAXON_LEVEL);
    }

    public function it_returns_max_children_per_taxon_level(): void
    {
        $this->getMaxChildrenPerTaxonLevel()->shouldReturn(self::MAX_CHILDREN_PER_TAXON_LEVEL);
    }

    public function it_returns_products_per_wishlist_qty(): void
    {
        $this->getProductsPerWishlistQty()->shouldReturn(self::PRODUCTS_PER_WISHLIST_QTY);
    }

    public function it_returns_stress(): void
    {
        $this->getStress()->shouldReturn(self::STRESS);
    }
}
