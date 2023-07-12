<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider;
use BitBag\SyliusVueStorefront2Plugin\DataProvider\PreFetcher\PreFetcherInterface;
use Doctrine\Persistence\ManagerRegistry;

final class CachedCollectionDataProvider extends CollectionDataProvider implements CachedCollectionDataProviderInterface
{
    private PreFetcherInterface $compositePreFetcher;

    public function __construct(
        PreFetcherInterface $preFetchedDataProvider,
        ManagerRegistry $managerRegistry,
        iterable $collectionExtensions = [],
    ) {
        parent::__construct($managerRegistry, $collectionExtensions);
        $this->compositePreFetcher = $preFetchedDataProvider;
    }

    public function getCollection(
        string $resourceClass,
        string $operationName = null,
        array $context = [],
    ): iterable {
        $collection = parent::getCollection($resourceClass, $operationName, $context);
        $ids = array_map(
            static fn ($object) => $object->getId(),
            (array)$collection->getIterator(),
        );

        $this->compositePreFetcher->preFetchData($ids, $context);

        return $collection;
    }

    public function getCachedData(
        string $identifier,
        array $context,
    ): array {
        return $this->compositePreFetcher->getPreFetchedData($identifier, $context);
    }
}
