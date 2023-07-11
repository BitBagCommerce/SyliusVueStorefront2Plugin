<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider\Subresource;

use ApiPlatform\State\Pagination\ArrayPaginator;
use BitBag\SyliusVueStorefront2Plugin\DataProvider\CachedCollectionDataProviderInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ProductImageSubresourceDataProvider implements RestrictedSubresourceDataProviderInterface
{
    public function __construct(private CachedCollectionDataProviderInterface $cachedCollectionDataProvider)
    {
    }

    public function supports(
        string $resourceClass,
        array $context,
        string $operationName = null
    ): bool {
        return is_a($resourceClass, ProductImageInterface::class, true);
    }

    public function getSubresource(
        string $resourceClass,
        array $identifiers,
        array $context,
        string $operationName = null
    ): ArrayPaginator {
        /** @var ProductInterface[] $data */
        $data = $this->cachedCollectionDataProvider->getCachedData();

        Assert::keyExists($identifiers, 'code');

        $product = null;

        foreach ($data as $datum) {
            if ($identifiers['code'] === $datum->getCode()) {
                $product = $datum;

                break;
            }
        }

        if ($product !== null) {
            return new ArrayPaginator($product->getImages()->toArray(), 0, $product->getImages()->count());
        }

        return new ArrayPaginator([], 0, 0);
    }
}
