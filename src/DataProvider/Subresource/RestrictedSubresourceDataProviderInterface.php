<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider\Subresource;

use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface;

interface RestrictedSubresourceDataProviderInterface extends SubresourceDataProviderInterface
{
    public function supports(
        string $resourceClass,
        array $context,
        string $operationName = null
    ): bool;
}
