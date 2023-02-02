<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Doctrine\Orm\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\Doctrine\Orm\Extension\WishlistsCurrentUserExtension;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProduct;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Security;

final class WishlistsCurrentUserExtensionSpec extends ObjectBehavior
{
    public function let(Security $security): void
    {
        $this->beConstructedWith($security);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistsCurrentUserExtension::class);
        $this->shouldHaveType(ContextAwareQueryCollectionExtensionInterface::class);
    }

    public function it_applies_to_collection_for_collection_query(
        Security $security,
        ShopUserInterface $shopUser,
        QueryBuilder $queryBuilder,
        Expr $expr,
        Comparison $comparison,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userId = 1;
        $security->getUser()->willReturn($shopUser);
        $shopUser->getId()->willReturn($userId);

        $expr->eq('alias.shopUser', ':currentUser')->willReturn($comparison);

        $queryBuilder->getRootAliases()->willReturn(['alias']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->andWhere($comparison)->shouldBeCalled();
        $queryBuilder->setParameter(':currentUser', $userId)->shouldBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            Wishlist::class,
            null,
            ['graphql_operation_name' => 'collection_query'],
        );
    }

    public function it_applies_to_collection_for_shop_get_wishlists(
        Security $security,
        ShopUserInterface $shopUser,
        QueryBuilder $queryBuilder,
        Expr $expr,
        Comparison $comparison,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userId = 1;
        $security->getUser()->willReturn($shopUser);
        $shopUser->getId()->willReturn($userId);

        $expr->eq('alias.shopUser', ':currentUser')->willReturn($comparison);

        $queryBuilder->getRootAliases()->willReturn(['alias']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->andWhere($comparison)->shouldBeCalled();
        $queryBuilder->setParameter(':currentUser', $userId)->shouldBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            Wishlist::class,
            null,
            ['collection_operation_name' => 'shop_get_wishlists'],
        );
    }

    public function it_applies_to_collection_for_another_class(
        QueryBuilder $queryBuilder,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            WishlistProduct::class,
            null,
            ['collection_operation_name' => 'shop_get_wishlists'],
        );
    }

    public function it_applies_to_collection_with_not_support_context(
        QueryBuilder $queryBuilder,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            Wishlist::class,
            null,
            ['graphql_operation_name' => 'item_query'],
        );
    }

    public function it_applies_to_collection_for_anonymous_user(
        QueryBuilder $queryBuilder,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
        Security $security,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::any(), Argument::any())->shouldNotBeCalled();
        $security->getUser()->willReturn(null);

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            Wishlist::class,
            null,
            ['graphql_operation_name' => 'collection_query'],
        );
    }
}
