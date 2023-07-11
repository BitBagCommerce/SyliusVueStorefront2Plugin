<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider;

final class CachedCollectionDataProvider extends CollectionDataProvider implements CachedCollectionDataProviderInterface
{
    private array $cachedData = [];

    public function getCollection(
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): iterable {
        $collection = parent::getCollection($resourceClass, $operationName, $context);

        foreach ($collection as $item) {
            $this->cachedData[] = $item;
        }

        return $collection;
    }

    public function getCachedData(): array
    {
        return $this->cachedData;
    }
}
