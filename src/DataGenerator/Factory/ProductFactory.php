<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use BitBag\SyliusVueStorefront2Plugin\Factory\ShopUserTokenFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\Model\ShopUserToken;
use BitBag\SyliusVueStorefront2Plugin\Model\ShopUserTokenInterface;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Ramsey\Uuid\Uuid;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ProductFactory implements ProductFactoryInterface
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

    public function create(string $channelCode): ProductInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $uuid = Uuid::uuid4()->toString();

        $product->setName(sprintf('Product %s', $uuid));
        $product->setSlug($this->faker->slug() . '-' . $uuid);
        $product->setCode(sprintf('Code-%s', $uuid));
        $product->setDescription($uuid);
        $product->setShortDescription($this->faker->sentence());
        $product->setEnabled(true);
        $product->setCreatedAt($this->faker->dateTimeBetween('-1 year'));

        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();
        $variant->setCode(sprintf('Code-%s', $uuid));
        $variant->setName(sprintf('Product variant %s', $uuid));

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($this->faker->randomNumber());
        $channelPricing->setChannelCode($channelCode);

        $variant->addChannelPricing($channelPricing);

        $product->addVariant($variant);

        return $product;
    }
}
