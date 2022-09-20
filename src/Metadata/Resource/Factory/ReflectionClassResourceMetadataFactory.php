<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Metadata\Resource\Factory;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

/** @experimental  */
final class ReflectionClassResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    private ?ResourceMetadataFactoryInterface $decorated;

    public function __construct(
        ResourceMetadataFactoryInterface $decorated = null
    ) {
        $this->decorated = $decorated;
    }

    /**
     * @inheritdoc
     *
     * @psalm-var class-string $resourceClass
     */
    public function create(string $resourceClass): ResourceMetadata
    {
        $parentResourceMetadata = null;
        if (null !== $this->decorated) {
            try {
                $parentResourceMetadata = $this->decorated->create($resourceClass);
            } catch (ResourceClassNotFoundException $resourceNotFoundException) {
                // Ignore not found exception from decorated factories
            }
        }

        if (null !== $parentResourceMetadata) {
            return $parentResourceMetadata;
        }

        if (!(class_exists($resourceClass) || interface_exists($resourceClass))) {
            return $this->handleNotFound($parentResourceMetadata, $resourceClass);
        }

        try {
            $reflectionClass = new \ReflectionClass($resourceClass);
        } catch (\ReflectionException $reflectionException) {
            return $this->handleNotFound($parentResourceMetadata, $resourceClass);
        }

        return $this->createFromReflection(new ResourceMetadata(), $reflectionClass);
    }

    /**
     * Returns the metadata from the decorated factory if available or throws an exception.
     *
     * @throws ResourceClassNotFoundException
     */
    private function handleNotFound(?ResourceMetadata $parentPropertyMetadata, string $resourceClass): ResourceMetadata
    {
        if (null !== $parentPropertyMetadata) {
            return $parentPropertyMetadata;
        }

        throw new ResourceClassNotFoundException(sprintf('Resource "%s" not found.', $resourceClass));
    }

    private function createFromReflection(ResourceMetadata $resourceMetadata, \ReflectionClass $reflectionClass): ResourceMetadata
    {
        return $resourceMetadata->withShortName($reflectionClass->getShortName());
    }
}
