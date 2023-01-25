<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Orm\Extension;


use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Security;

final class WishlistsCurrentUserExtension implements QueryCollectionExtensionInterface
{
    const GRAPHQL_OPERATION_KEY = 'graphql_operation_name';
    const COLLECTION_OPERATION_KEY = 'collection_operation_name';
    const WISHLIST_GRAPHQL_OPERATION_NAME = 'collection_query';
    const WISHLIST_COLLECTION_OPERATION_NAME = 'shop_get_wishlists';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {

        if (false === is_a($resourceClass, Wishlist::class, true)) {
            return;
        }

        if (array_key_exists(self::COLLECTION_OPERATION_KEY, $context)
            && self::WISHLIST_COLLECTION_OPERATION_NAME !== $context[self::COLLECTION_OPERATION_KEY]) {
            return;
        }

        if (array_key_exists(self::GRAPHQL_OPERATION_KEY, $context)
            && self::WISHLIST_GRAPHQL_OPERATION_NAME !== $context[self::GRAPHQL_OPERATION_KEY]) {
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
