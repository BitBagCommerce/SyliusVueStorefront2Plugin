<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\DataGenerator;

use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class WishlistRepositoryTest extends JsonApiTestCase
{
    public function test_find_by_channel_with_limit(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $wishlists = $repository->findByChannel($channel, 3);

        $this->assertCount(3, $wishlists);

        $this->assertSame('wishlist_1', $wishlists[0]->getName());
        $this->assertSame('wishlist_2', $wishlists[1]->getName());
        $this->assertSame('wishlist_3', $wishlists[2]->getName());
    }

    public function test_find_by_channel_with_offset(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $wishlists = $repository->findByChannel($channel, null, 1);

        $this->assertCount(3, $wishlists);

        $this->assertSame('wishlist_2', $wishlists[0]->getName());
        $this->assertSame('wishlist_3', $wishlists[1]->getName());
        $this->assertSame('wishlist_5', $wishlists[2]->getName());
    }

    public function test_find_by_channel_with_limit_and_offset(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $wishlists = $repository->findByChannel($channel, 2, 2);

        $this->assertCount(2, $wishlists);

        $this->assertSame('wishlist_3', $wishlists[0]->getName());
        $this->assertSame('wishlist_5', $wishlists[1]->getName());
    }

    public function test_find_by_channel_with_offset_exceeded(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $wishlists = $repository->findByChannel($channel, 2, 20);

        $this->assertCount(0, $wishlists);
    }

    public function test_getting_entity_count(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $entityCount = $repository->getEntityCount($channel);

        $this->assertEquals(4, $entityCount);
    }

    private function loadFixtures(): void
    {
        $this->loadFixturesFromFile('DataGenerator/wishlist_repository.yml');
    }

    private function getRepository(): WishlistRepositoryInterface
    {
        return $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.wishlist_repository');
    }

    private function getDefaultChannel(): ChannelInterface
    {
        return $this->getContainer()
            ->get('sylius.repository.channel')
            ->findOneByCode('channel_1');
    }
}
