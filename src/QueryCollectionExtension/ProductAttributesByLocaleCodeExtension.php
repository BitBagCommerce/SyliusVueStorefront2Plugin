<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;

final class ProductAttributesByLocaleCodeExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, ProductAttributeValueInterface::class, true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $localeCode = $context[ContextKeys::LOCALE_CODE];

        $queryBuilder
            ->andWhere(sprintf('%s.localeCode = :localeCode', $rootAlias))
            ->setParameter('localeCode', $localeCode)
        ;
    }
}
