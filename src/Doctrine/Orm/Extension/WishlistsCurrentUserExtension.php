<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Orm\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Security;

final class WishlistsCurrentUserExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public const GRAPHQL_OPERATION_KEY = 'graphql_operation_name';

    public const COLLECTION_OPERATION_KEY = 'collection_operation_name';

    public const WISHLIST_GRAPHQL_OPERATION_NAME = 'collection_query';

    public const WISHLIST_COLLECTION_OPERATION_NAME = 'shop_get_wishlists';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @phpstan-ignore-next-line The interface's method doesn't have return type defined */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = [],
    ) {
        if (false === is_a($resourceClass, Wishlist::class, true)) {
            return;
        }

        if (array_key_exists(self::COLLECTION_OPERATION_KEY, $context) &&
            self::WISHLIST_COLLECTION_OPERATION_NAME !== $context[self::COLLECTION_OPERATION_KEY]) {
            return;
        }

        if (array_key_exists(self::GRAPHQL_OPERATION_KEY, $context) &&
            self::WISHLIST_GRAPHQL_OPERATION_NAME !== $context[self::GRAPHQL_OPERATION_KEY]) {
            return;
        }

        /** @var ShopUserInterface|null $user */
        $user = $this->security->getUser();
        if (null === $user) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.shopUser = :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $user->getId());
    }
}
