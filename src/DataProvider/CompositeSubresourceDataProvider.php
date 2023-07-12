<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider;

use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface;
use BitBag\SyliusVueStorefront2Plugin\DataProvider\Subresource\RestrictedSubresourceDataProviderInterface;
use Webmozart\Assert\Assert;

final class CompositeSubresourceDataProvider implements SubresourceDataProviderInterface
{
    public function __construct(
        private SubresourceDataProviderInterface $decoratedSubresourceProvider,
        private iterable $subresourceProviders = [],
    ) {
        Assert::allIsInstanceOf($this->subresourceProviders, RestrictedSubresourceDataProviderInterface::class);
    }

    public function getSubresource(
        string $resourceClass,
        array $identifiers,
        array $context,
        string $operationName = null
    ): iterable|object|null {
        /** @var RestrictedSubresourceDataProviderInterface $provider */
        foreach ($this->subresourceProviders as $provider) {
            if ($provider->supports($resourceClass, $context, $operationName)) {
                return $provider->getSubresource($resourceClass, $identifiers, $context, $operationName);
            }
        }

        return $this->decoratedSubresourceProvider->getSubresource($resourceClass, $identifiers, $context, $operationName);
    }
}
