<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider;

use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface as BaseSubresourceDataProviderInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

interface SubresourceDataProviderInterface extends BaseSubresourceDataProviderInterface
{
    const ELIGIBLE_ENTITIES = [
        ChannelPricingInterface::class,
        ProductAttributeValueInterface::class,
        ProductImageInterface::class,
        ProductOptionInterface::class,
        ProductOptionValueInterface::class,
        ProductVariantInterface::class,
    ];
}
