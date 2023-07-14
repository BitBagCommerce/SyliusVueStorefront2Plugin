<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider\Subresource;

use ApiPlatform\State\Pagination\ArrayPaginator;
use BitBag\SyliusVueStorefront2Plugin\DataProvider\CachedCollectionDataProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductVariantSubresourceDataProvider implements RestrictedSubresourceDataProviderInterface
{
    public function __construct(private CachedCollectionDataProviderInterface $cachedCollectionDataProvider)
    {
    }

    public function supports(
        string $resourceClass,
        array $context,
        string $operationName = null,
    ): bool {
        return is_a($resourceClass, ProductVariantInterface::class, true);
    }

    public function getSubresource(
        string $resourceClass,
        array $identifiers,
        array $context,
        string $operationName = null,
    ): ArrayPaginator {
        Assert::keyExists($identifiers, 'code');

        /** @var ProductInterface[] $data */
        $data = $this->cachedCollectionDataProvider->getCachedData($identifiers['code'], $context);

        return new ArrayPaginator($data, 0, count($data));
    }
}
