<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelPricingInterface;

final class ChannelPricingChannelCodeFilter extends AbstractContextAwareFilter
{
    const PROPERTY_NAME = 'channelCode';

    /** @phpstan-ignore-next-line The abstract class' method doesn't have return type defined */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        if (self::PROPERTY_NAME !== $property) {
            return;
        }

        if (!is_string($value)) {
            return;
        }

        if (!is_a($resourceClass, ChannelPricingInterface::class, true)) {
            return;
        }

        $parameterName = $queryNameGenerator->generateParameterName($property);

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.%s LIKE :%s', $rootAlias, $property, $parameterName));
        $queryBuilder->setParameter($parameterName, $value);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            self::PROPERTY_NAME => [
                'type' => 'string',
                'required' => true,
                'property' => null,
                'swagger' => [
                    'name' => 'Channel pricing filter',
                    'description' => 'Get a collection of channel pricing for channelCode',
                ],
            ]
        ];
    }
}
