<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ConsoleCommand;

interface BulkDataGeneratorInterface
{
    const DEFAULT_TAXONS_QTY = 5000;

    const DEFAULT_MAX_TAXON_LEVEL = 20;

    const DEFAULT_MAX_CHILDREN_PER_TAXON_LEVEL = 5;

    const DEFAULT_WISHLISTS_QTY = 10000;

    const DEFAULT_PRODUCTS_QTY = 100000;

    const DEFAULT_PRODUCTS_PER_TAXON_QTY = 1000;

    const DEFAULT_PRODUCTS_PER_WISHLIST_QTY = 1000;
}
