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

interface DataGeneratorCommandContextInterface extends ContextInterface
{
    public function getIO(): SymfonyStyle;

    public function getChannel(): ChannelInterface;

    public function getProductsQty(): int;

    public function getTaxonsQty(): int;

    public function getWishlistsQty(): int;

    public function getProductsPerTaxonQty(): int;

    public function getMaxTaxonLevel(): int;

    public function getMaxChildrenPerTaxonLevel(): int;

    public function getProductsPerWishlistQty(): int;
}
