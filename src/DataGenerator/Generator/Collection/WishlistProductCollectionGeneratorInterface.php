<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;

interface WishlistProductCollectionGeneratorInterface
{
    const LIMIT = 100;

    const FLUSH_AFTER = 100;

    public function generate(
        WishlistInterface $wishlist,
        ContextInterface $context,
    ): void;
}
