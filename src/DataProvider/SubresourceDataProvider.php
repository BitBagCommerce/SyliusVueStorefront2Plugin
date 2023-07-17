<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider;

use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface as BaseSubresourceDataProviderInterface;
use ApiPlatform\State\Pagination\ArrayPaginator;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class SubresourceDataProvider implements SubresourceDataProviderInterface
{
    public function __construct(
        private BaseSubresourceDataProviderInterface $decoratedSubresourceProvider,
        private CachedCollectionDataProviderInterface $cachedCollectionDataProvider
    ) {
    }

    public function getSubresource(
        string $resourceClass,
        array $identifiers,
        array $context,
        string $operationName = null
    ): iterable|object|null {
        if ($this->supports($resourceClass)) {
            Assert::keyExists($identifiers, 'code');

            /** @var ProductInterface[] $data */
            $data = $this->cachedCollectionDataProvider->getCachedData($identifiers['code'], $context);

            return new ArrayPaginator($data, 0, count($data));
        }

        return $this->decoratedSubresourceProvider->getSubresource($resourceClass, $identifiers, $context, $operationName);
    }

    private function supports(string $resourceClass): bool
    {
        foreach (self::ELIGIBLE_ENTITIES as $entity) {
            if (is_a($resourceClass, $entity, true)) {
                return true;
            }
        }

        return false;
    }
}
