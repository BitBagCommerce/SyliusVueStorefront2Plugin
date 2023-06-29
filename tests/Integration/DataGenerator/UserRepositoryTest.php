<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\DataGenerator;

use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\NoShopUserFoundException;
use Sylius\Component\Core\Model\ShopUserInterface;

final class UserRepositoryTest extends JsonApiTestCase
{
    public function test_getting_random_shop_user(): void
    {
        $this->loadFixturesFromFile('DataGenerator/user_repository.yml');

        $repository = $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.user_repository');

        $shopUser = $repository->getRandomShopUser();
        $this->assertInstanceOf(ShopUserInterface::class, $shopUser);
    }

    public function test_not_finding_shop_user(): void
    {
        $this->expectException(NoShopUserFoundException::class);

        $repository = $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.user_repository');

        $repository->getRandomShopUser();
    }
}
