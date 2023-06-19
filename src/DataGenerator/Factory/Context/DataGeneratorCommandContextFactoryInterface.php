<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

interface DataGeneratorCommandContextFactoryInterface
{
    public function fromInput(
        SymfonyStyle $io,
        ChannelInterface $channel,
        int $productsQty,
        int $taxonsQty,
        int $wishlistsQty,
        int $productsPerTaxonQty,
        int $maxTaxonLevel,
        int $maxChildrenPerTaxonLevel,
        int $productsPerWishlistQty,
    ): DataGeneratorCommandContextInterface;
}
