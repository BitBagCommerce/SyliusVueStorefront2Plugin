<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class WishlistFactory extends Factory
{
    private FactoryInterface $wishlistFactory;
    private ChannelInterface $channel;

    public function __construct(
        EntityManagerInterface $entityManager,
        InputInterface $input,
        OutputInterface $output,
        FactoryInterface $wishlistFactory,
        ChannelInterface $channel,
    ) {
        parent::__construct($entityManager, $input, $output);

        $this->wishlistFactory = $wishlistFactory;
        $this->channel = $channel;
    }

    public function entityName(): string
    {
        return 'Wishlist';
    }

    public function create(): WishlistInterface
    {
        $uuid = $this->faker->uuid;

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();
        $wishlist->setName('Wishlist ' . $uuid);
        $wishlist->setChannel($this->channel);
        $wishlist->setToken($this->faker->windowsPlatformToken);

        return $wishlist;
    }
}
