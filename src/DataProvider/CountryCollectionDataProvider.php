<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/** @experimental */
final class CountryCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private RepositoryInterface $countryRepository;

    private PaginationExtension $paginationExtension;

    /** @see QueryCollectionExtensionInterface */
    private iterable $collectionExtensions;

    private QueryNameGeneratorInterface $queryNameGenerator;

    public function __construct(
        RepositoryInterface $countryRepository,
        PaginationExtension $paginationExtension,
        QueryNameGeneratorInterface $queryNameGenerator,
        iterable $collectionExtensions
    ) {
        $this->countryRepository = $countryRepository;
        $this->paginationExtension = $paginationExtension;
        $this->queryNameGenerator = $queryNameGenerator;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, CountryInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        if (!method_exists($this->countryRepository, 'createQueryBuilder')) {
            throw new \RuntimeException('The repository class must have a "createQueryBuilder" method.');
        }
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->countryRepository->createQueryBuilder('o');

        /** @var QueryCollectionExtensionInterface $extension */
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $this->queryNameGenerator, $resourceClass, $operationName);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                return $extension->getResult($queryBuilder);
            }
        }

        return $this->paginationExtension->getResult(
            $queryBuilder,
            $resourceClass,
            $operationName,
            $context
        );
    }
}
