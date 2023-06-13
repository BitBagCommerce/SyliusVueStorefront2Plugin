<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductGenerator implements GeneratorInterface
{
    private ProductFactory $productFactory;
    private ChannelInterface $channel;

    private EntityManagerInterface $entityManager;

    protected Generator $faker;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductFactory $productFactory,
        ChannelInterface $channel,
    ) {
        $this->entityManager = $entityManager;
        $this->faker = FakerFactory::create();

        $this->productFactory = $productFactory;
        $this->channel = $channel;
    }

    public function entityName(): string
    {
        return 'Product';
    }

    public function generate(): ProductInterface
    {
        return $this->productFactory->create(
            $this->faker->uuid,
            $this->faker->sentence(10),
            $this->faker->sentence(3),
            $this->faker->randomNumber(),
            $this->channel,
            $this->faker->dateTimeBetween('-1 year')
        );
    }
}
