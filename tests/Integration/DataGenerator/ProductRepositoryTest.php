<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\DataGenerator;

use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ProductRepositoryTest extends JsonApiTestCase
{
    public function test_find_by_channel_with_limit(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $products = $repository->findByChannel($channel, 3);

        $this->assertCount(3, $products);

        $this->assertSame('product_1', $products[0]->getCode());
        $this->assertSame('product_2', $products[1]->getCode());
        $this->assertSame('product_3', $products[2]->getCode());
    }

    public function test_find_by_channel_with_offset(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $products = $repository->findByChannel($channel, null, 1);

        $this->assertCount(3, $products);

        $this->assertSame('product_2', $products[0]->getCode());
        $this->assertSame('product_3', $products[1]->getCode());
        $this->assertSame('product_5', $products[2]->getCode());
    }

    public function test_find_by_channel_with_limit_and_offset(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $products = $repository->findByChannel($channel, 2, 2);

        $this->assertCount(2, $products);

        $this->assertSame('product_3', $products[0]->getCode());
        $this->assertSame('product_5', $products[1]->getCode());
    }

    public function test_find_by_channel_with_offset_exceeded(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();
        $channel = $this->getDefaultChannel();

        $products = $repository->findByChannel($channel, 2, 20);

        $this->assertCount(0, $products);
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
        $this->loadFixturesFromFile('DataGenerator/product_repository.yml');
    }

    private function getRepository(): ProductRepositoryInterface
    {
        return $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.product_repository');
    }

    private function getDefaultChannel(): ChannelInterface
    {
        return $this->getContainer()
            ->get('sylius.repository.channel')
            ->findOneByCode('channel_1');
    }
}
