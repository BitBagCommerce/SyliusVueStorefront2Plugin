<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusVueStorefront2Plugin\Doctrine\Orm\Extension\WishlistsCurrentUserExtension;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Component\Security\Core\Security;

final class WishlistsCurrentUserExtensionTest extends JsonApiTestCase
{
    public function test_it_does_nothing_for_collection_when_current_resource_is_not_a_wishlist(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->expects(self::never())
            ->method('getUser')
        ;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects(self::never())
            ->method('andWhere')
        ;
        $queryNameGenerator = $this->createMock(LegacyQueryNameGeneratorInterface::class);

        $productVariantCurrentVendorExtension = new WishlistsCurrentUserExtension($security);
        $productVariantCurrentVendorExtension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            WishlistProductInterface::class,
        );
    }

    public function test_it_does_nothing_for_collection_when_context_is_different(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->expects(self::never())
            ->method('getUser')
        ;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects(self::never())
            ->method('andWhere')
        ;
        $queryNameGenerator = $this->createMock(LegacyQueryNameGeneratorInterface::class);

        $productVariantCurrentVendorExtension = new WishlistsCurrentUserExtension($security);
        $productVariantCurrentVendorExtension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            WishlistInterface::class,
            null,
            ['item_operation_name' => 'shop_get_wishlist'],
        );
    }

    public function test_it_does_nothing_for_collection_for_graphql_wishlists_context(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->method('getUser')
            ->willReturn(null)
        ;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects(self::never())
            ->method('andWhere')
        ;
        $queryNameGenerator = $this->createMock(LegacyQueryNameGeneratorInterface::class);

        $productVariantCurrentVendorExtension = new WishlistsCurrentUserExtension($security);
        $productVariantCurrentVendorExtension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            Wishlist::class,
            null,
            ['graphql_operation_name' => 'collection_query'],
        );
    }

    public function test_it_does_nothing_for_collection_for_rest_wishlists_context(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->method('getUser')
            ->willReturn(null)
        ;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects(self::never())
            ->method('andWhere')
        ;
        $queryNameGenerator = $this->createMock(LegacyQueryNameGeneratorInterface::class);

        $productVariantCurrentVendorExtension = new WishlistsCurrentUserExtension($security);
        $productVariantCurrentVendorExtension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            Wishlist::class,
            null,
            ['collection_operation_name' => 'shop_get_wishlists'],
        );
    }

    public function test_it_filters_resources_when_getting_collection_by_user(): void
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $wishlistRepository = $entityManager->getRepository(Wishlist::class);
        $shopUserRepository = $entityManager->getRepository(ShopUser::class);

        $this->loadFixturesFromFile('WishlistsCurrentUserExtensionTest/wishlists_current_user_extension.yml');

        $shopUser = $shopUserRepository->findOneByEmail('bruce.wayne@example.com');
        $security = $this->createMock(Security::class);
        $security
            ->method('getUser')
            ->willReturn($shopUser)
        ;

        $queryBuilder = $wishlistRepository->createQueryBuilder('o');
        $queryNameGenerator = $this->createMock(LegacyQueryNameGeneratorInterface::class);

        $productVariantCurrentVendorExtension = new WishlistsCurrentUserExtension($security);
        $productVariantCurrentVendorExtension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            Wishlist::class,
            null,
            ['collection_operation_name' => 'shop_get_wishlists'],
        );

        $result = $queryBuilder->getQuery()->getResult();
        self::assertCount(2, $result);
    }
}
