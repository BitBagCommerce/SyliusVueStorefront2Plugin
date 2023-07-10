<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;

interface CachedCollectionDataProviderInterface extends ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function getCachedData(): array;
}
