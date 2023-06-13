<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductFactory
{
    private FactoryInterface $productFactory;
    private FactoryInterface $productVariantFactory;
    private FactoryInterface $channelPricingFactory;

    public function __construct(
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $channelPricingFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->channelPricingFactory = $channelPricingFactory;
    }

    public function entityName(): string
    {
        return 'Product';
    }

    public function create(
        string $uuid,
        string $description,
        string $shortDescrption,
        int $price,
        ChannelInterface $channel,
        \DateTimeInterface $createdAt
    ): ProductInterface {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setName('Product ' . $uuid);
        $product->setSlug($uuid);
        $product->setCode('code-' . $uuid);
        $product->setDescription($description);
        $product->setShortDescription($shortDescrption);
        $product->setEnabled(true);
        $product->setCreatedAt($createdAt);
        $product->addChannel($channel);
        $product->addVariant($this->createVariant($uuid, $price, $channel));

        return $product;
    }

    private function createVariant(
        string $uuid,
        int $price,
        ChannelInterface $channel
    ): ProductVariantInterface {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();
        $variant->setName('Product variant ' . $uuid);
        $variant->setCode('code-' . $uuid);

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($price);
        $channelPricing->setChannelCode($channel->getCode());

        $variant->addChannelPricing($channelPricing);

        return $variant;
    }
}
