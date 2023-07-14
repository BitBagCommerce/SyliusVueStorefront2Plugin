<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use DateTimeInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

interface ProductFactoryInterface
{
    public function create(
        string $name,
        string $code,
        string $description,
        string $shortDescription,
        ProductVariantInterface $variant,
        ChannelInterface $channel,
        DateTimeInterface $createdAt,
        TaxonInterface $mainTaxon,
    ): ProductInterface;
}