<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\EntityGenerator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\EntityContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\WishlistContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Faker\Factory;
use Faker\Generator;

final class WishlistGenerator implements GeneratorInterface
{
    private WishlistFactoryInterface $wishlistFactory;

    protected Generator $faker;

    public function __construct(WishlistFactoryInterface $wishlistFactory)
    {
        $this->wishlistFactory = $wishlistFactory;
        $this->faker = Factory::create();
    }

    public function generate(EntityContextInterface $context): WishlistInterface
    {
        if (!$context instanceof WishlistContextInterface) {
            throw new InvalidContextException();
        }

        return $this->wishlistFactory->create(
            $this->faker->sentence(3),
            md5($this->faker->sentence(10)),
            $context->getChannel(),
        );
    }
}
