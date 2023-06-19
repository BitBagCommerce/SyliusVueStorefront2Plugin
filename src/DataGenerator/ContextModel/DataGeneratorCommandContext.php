<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DataGeneratorCommandContext implements DataGeneratorCommandContextInterface
{
    private SymfonyStyle $io;

    private ChannelInterface $channel;

    private int $productsQty;

    private int $taxonsQty;

    private int $wishlistsQty;

    private int $productsPerTaxonQty;

    private int $maxTaxonLevel;

    private int $maxChildrenPerTaxonLevel;

    private int $productsPerWishlistQty;

    public function __construct(
        SymfonyStyle $io,
        ChannelInterface $channel,
        int $productsQty,
        int $taxonsQty,
        int $wishlistsQty,
        int $productsPerTaxonQty,
        int $maxTaxonLevel,
        int $maxChildrenPerTaxonLevel,
        int $productsPerWishlistQty
    ) {
        $this->io = $io;
        $this->channel = $channel;
        $this->productsQty = $productsQty;
        $this->taxonsQty = $taxonsQty;
        $this->wishlistsQty = $wishlistsQty;
        $this->productsPerTaxonQty = $productsPerTaxonQty;
        $this->maxTaxonLevel = $maxTaxonLevel;
        $this->maxChildrenPerTaxonLevel = $maxChildrenPerTaxonLevel;
        $this->productsPerWishlistQty = $productsPerWishlistQty;
    }

    public function getIO(): SymfonyStyle
    {
        return $this->io;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getProductsQty(): int
    {
        return $this->productsQty;
    }

    public function getTaxonsQty(): int
    {
        return $this->taxonsQty;
    }

    public function getWishlistsQty(): int
    {
        return $this->wishlistsQty;
    }

    public function getProductsPerTaxonQty(): int
    {
        return $this->productsPerTaxonQty;
    }

    public function getMaxTaxonLevel(): int
    {
        return $this->maxTaxonLevel;
    }

    public function getMaxChildrenPerTaxonLevel(): int
    {
        return $this->maxChildrenPerTaxonLevel;
    }

    public function getProductsPerWishlistQty(): int
    {
        return $this->productsPerWishlistQty;
    }
}
