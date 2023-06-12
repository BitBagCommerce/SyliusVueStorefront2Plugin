<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ProductFactory extends Factory
{
    private FactoryInterface $productFactory;
    private FactoryInterface $productVariantFactory;
    private FactoryInterface $channelPricingFactory;
    private ChannelInterface $channel;

    public function __construct(
        EntityManagerInterface $entityManager,
        InputInterface $input,
        OutputInterface $output,
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $channelPricingFactory,
        ChannelInterface $channel,
    ) {
        parent::__construct($entityManager, $input, $output);

        $this->productFactory = $productFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->channelPricingFactory = $channelPricingFactory;
        $this->channel = $channel;
    }

    public function entityName(): string
    {
        return 'Product';
    }

    public function create(): ProductInterface
    {
        $uuid = $this->faker->uuid;

        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setName('Product ' . $uuid);
        $product->setSlug($uuid);
        $product->setCode('code-' . $uuid);
        $product->setDescription((string)$this->faker->paragraphs(3, true));
        $product->setShortDescription($this->faker->sentence());
        $product->setEnabled(true);
        $product->setCreatedAt($this->faker->dateTimeBetween('-1 year'));
        $product->addChannel($this->channel);
        $product->addVariant($this->createVariant($uuid));

        return $product;
    }

    private function createVariant(string $uuid): ProductVariantInterface
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();
        $variant->setName('Product variant ' . $uuid);
        $variant->setCode('code-' . $uuid);

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($this->faker->randomNumber());
        $channelPricing->setChannelCode($this->channel->getCode());

        $variant->addChannelPricing($channelPricing);

        return $variant;
    }
}
