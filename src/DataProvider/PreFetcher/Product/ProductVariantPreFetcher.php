<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider\PreFetcher\Product;

use BitBag\SyliusVueStorefront2Plugin\DataProvider\PreFetcher\RestrictedPreFetcherInterface;
use BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\Product\ProductVariantRepositoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ProductVariantPreFetcher implements RestrictedPreFetcherInterface
{
    private ProductVariantRepositoryInterface $repository;

    private array $preFetchedData = [];

    public function __construct(ProductVariantRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function preFetchData(
        array $parentIds,
        array $context,
    ): void {
        /** @var ProductVariantInterface $result */
        foreach ($this->repository->findByProductIds($parentIds, $context) as $result) {
            $this->preFetchedData[$result->getProduct()?->getCode()][] = $result;
        }
    }

    public function getPreFetchedData(
        string $identifier,
        ?array $context = [],
    ): array {
        return $this->preFetchedData[$identifier] ?? [];
    }

    public function supports(
        array $context,
        ?string $attribute = null,
    ): bool {
        $resourceClass = $context['resource_class'];

        return is_a($resourceClass, ProductVariantInterface::class, true)
            || ($attribute === 'variants' && is_a($resourceClass, ProductInterface::class, true));
    }
}
