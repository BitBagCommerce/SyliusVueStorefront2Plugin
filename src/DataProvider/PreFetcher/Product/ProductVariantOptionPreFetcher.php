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
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

class ProductVariantOptionPreFetcher implements RestrictedPreFetcherInterface, ProductOptionsPreFetcherInterface
{
    private ProductVariantRepositoryInterface $repository;

    private bool $isPrefetched = false;

    private array $variantOptionValues = [];

    private array $productOptions = [];

    private array $optionValues = [];

    public function __construct(ProductVariantRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function preFetchData(
        array $parentIds,
        array $context,
    ): void {
        if ($this->isPrefetched === true) {
            return;
        }

        $result = $this->repository->findOptionsByProductIds($parentIds, $context);

        /** @var ProductVariantInterface $result */
        foreach ($result as $variant) {
            $this->prepareProductOptions($variant);
            $this->prepareOptionValues($variant);
            $this->prepareVariantOptionValues($variant);
        }

        $this->isPrefetched = true;
    }

    public function getPreFetchedData(
        string $identifier,
        array $context,
    ): array {
        return match ($context['property']) {
            self::ELIGIBLE_ATTR_PRODUCT_OPTIONS => $this->productOptions[$identifier] ?? [],
            self::ELIGIBLE_ATTR_PRODUCT_OPTION_VALUES => $this->optionValues[$identifier] ?? [],
            self::ELIGIBLE_ATTR_VARIANT_OPTION_VALUES => $this->variantOptionValues[$identifier] ?? [],
            default => [],
        };
    }

    public function supports(
        array $context,
        ?string $attribute = null,
    ): bool {
        $resourceClass = $context['resource_class'];

        return is_a($resourceClass, ProductOptionInterface::class, true)
            || is_a($resourceClass, ProductOptionValueInterface::class, true)
            || (in_array($attribute, self::ELIGIBLE_ATTRIBUTES, true)
                && is_a($resourceClass, ProductInterface::class, true));
    }

    private function prepareProductOptions(ProductVariantInterface $variant): void
    {
        foreach ($variant->getOptionValues() as $optionValue) {
            $option = $optionValue->getOption();
            $this->productOptions[$variant->getProduct()?->getCode()][$option?->getCode()] = $option;
        }
    }

    private function prepareOptionValues(ProductVariantInterface $variant): void
    {
        foreach ($variant->getOptionValues() as $optionValue) {
            $this->optionValues[$optionValue->getOption()?->getCode()][$optionValue?->getCode()] = $optionValue;
        }
    }

    private function prepareVariantOptionValues(ProductVariantInterface $variant): void
    {
        $this->variantOptionValues[$variant->getCode()] = $variant->getOptionValues()->toArray();
    }
}
