<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\WishlistContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Model\ChannelInterface;

final class WishlistGenerator implements GeneratorInterface
{
    private WishlistFactoryInterface $wishlistFactory;

    protected Generator $faker;

    public function __construct(WishlistFactoryInterface $wishlistFactory)
    {
        $this->wishlistFactory = $wishlistFactory;
        $this->faker = Factory::create();
    }

    public function generate(ContextInterface $context): WishlistInterface
    {
        assert($context instanceof WishlistContextInterface);

        return $this->wishlistFactory->create(
            $this->faker->words(3, true),
            md5($this->faker->words(10, true)),
            $context->getChannel(),
        );
    }
}
