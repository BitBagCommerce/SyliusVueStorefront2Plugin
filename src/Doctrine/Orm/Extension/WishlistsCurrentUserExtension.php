<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Orm\Extension;


use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class WishlistsCurrentUserExtension implements QueryCollectionExtensionInterface
{
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
        if (false === is_a($resourceClass, Wishlist::class, true) || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.shopUser = :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $user->getId());
    }
}
