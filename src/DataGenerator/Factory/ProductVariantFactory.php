<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductVariantFactory implements ProductVariantFactoryInterface
{
    private FactoryInterface $productVariantFactory;

    public function __construct(FactoryInterface $productVariantFactory)
    {
        $this->productVariantFactory = $productVariantFactory;
    }

    public function create(
        string $name,
        string $code,
        ChannelPricingInterface $channelPricing,
    ): ProductVariantInterface {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();
        $variant->setName($name);
        $variant->setCode($code);
        $variant->addChannelPricing($channelPricing);

        return $variant;
    }
}
