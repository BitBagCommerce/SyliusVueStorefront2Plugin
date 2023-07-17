<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider\PreFetcher\Product;

use BitBag\SyliusVueStorefront2Plugin\DataProvider\PreFetcher\RestrictedPreFetcherInterface;
use BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\Product\ProductImageRepositoryInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;

class ProductImagePreFetcher implements RestrictedPreFetcherInterface
{
    private ProductImageRepositoryInterface $repository;

    private array $preFetchedData = [];

    public function __construct(ProductImageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function preFetchData(
        array $parentIds,
        array $context,
    ): void {
        foreach ($this->repository->findByProductIds($parentIds, $context) as $result) {
            $this->preFetchedData[$result['code']][] = $result[0];
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

        return is_a($resourceClass, ProductImageInterface::class, true)
            || ($attribute === 'images' && is_a($resourceClass, ProductInterface::class, true));
    }
}
