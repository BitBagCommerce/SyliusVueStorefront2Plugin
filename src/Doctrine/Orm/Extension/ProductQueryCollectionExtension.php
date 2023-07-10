<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Orm\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductQueryCollectionExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
    ): void {
        if ($operationName === 'collection_query' && is_a($resourceClass, ProductInterface::class, true)) {
            $queryBuilder
                ->addSelect('oi')
                ->addSelect('ovs')
                ->addSelect('oav')
                ->join('o.variants', 'ovs')
                ->leftJoin('o.attributes', 'oav')
                ->leftJoin('o.images', 'oi');
        }
    }
}
