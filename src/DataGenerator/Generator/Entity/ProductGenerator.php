<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\EntityContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\ProductContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ChannelPricingFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductVariantFactoryInterface;
use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductGenerator implements GeneratorInterface
{
    private ProductFactoryInterface $productFactory;

    private ProductVariantFactoryInterface $productVariantFactory;

    private ChannelPricingFactoryInterface $channelPricingFactory;

    private Generator $faker;

    public function __construct(
        ProductFactoryInterface $productFactory,
        ProductVariantFactoryInterface $productVariantFactory,
        ChannelPricingFactoryInterface $channelPricingFactory,
    ) {
        $this->productFactory = $productFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->channelPricingFactory = $channelPricingFactory;
        $this->faker = Factory::create();
    }

    public function generate(EntityContextInterface $context): ProductInterface
    {
        if (!$context instanceof ProductContextInterface) {
            throw new InvalidContextException();
        }

        $channelPricing = $this->channelPricingFactory->create(
            $this->faker->randomNumber(),
            $context->getChannel(),
        );

        $variant = $this->productVariantFactory->create(
            $this->faker->sentence(3),
            $this->faker->uuid,
            $channelPricing,
        );

        return $this->productFactory->create(
            $this->faker->sentence(3),
            $this->faker->uuid,
            $this->faker->sentence(15),
            $this->faker->sentence(),
            $variant,
            $context->getChannel(),
            $this->faker->dateTimeBetween('-1 year')
        );
    }
}
