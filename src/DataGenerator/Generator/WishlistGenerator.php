<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\WishlistFactory;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistGenerator implements GeneratorInterface
{
    private WishlistFactory $factory;

    private ChannelInterface $channel;

    public function __construct(
        EntityManagerInterface $entityManager,
        FactoryInterface $wishlistFactory,
        ChannelInterface $channel,
    ) {
        parent::__construct($entityManager);

        $this->wishlistFactory = $wishlistFactory;
        $this->channel = $channel;
    }

    public function entityName(): string
    {
        return 'Wishlist';
    }

    public function generate(): WishlistInterface
    {
        return $this->factory->create($this->faker->uuid,
            $this->faker->windowsPlatformToken,
            $this->channel
        );
    }
}
