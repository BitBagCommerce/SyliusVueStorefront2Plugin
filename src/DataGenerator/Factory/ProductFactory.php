<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductFactory implements ProductFactoryInterface
{
    private FactoryInterface $productFactory;
    private FactoryInterface $productVariantFactory;
    private FactoryInterface $channelPricingFactory;
    private Generator $faker;

    public function __construct(
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $channelPricingFactory,
    ) {
        $this->productFactory = $productFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->channelPricingFactory = $channelPricingFactory;

        $this->faker = Factory::create();
    }

    public function create(ChannelInterface $channel): ProductInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $uuid = $this->faker->uuid;

        $product->setName('Product ' . $uuid);
        $product->setSlug($uuid);
        $product->setCode('code-' . $uuid);
        $product->setDescription($this->faker->paragraphs(3, true));
        $product->setShortDescription($this->faker->sentence());
        $product->setEnabled(true);
        $product->setCreatedAt($this->faker->dateTimeBetween('-1 year'));
        $product->addChannel($channel);
        $product->addVariant($this->createVariant($channel, $uuid));

        return $product;
    }

    private function createVariant(ChannelInterface $channel, string $uuid): ProductVariantInterface
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();
        $variant->setName('Product variant ' . $uuid);
        $variant->setCode('code-' . $uuid);

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($this->faker->randomNumber());
        $channelPricing->setChannelCode($channel->getCode());

        $variant->addChannelPricing($channelPricing);

        return $variant;
    }
}
