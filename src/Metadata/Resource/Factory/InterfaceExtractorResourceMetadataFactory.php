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
use ApiPlatform\Metadata\Extractor\ResourceExtractorInterface;

/** @experimental */
final class InterfaceExtractorResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    private ?ResourceMetadataFactoryInterface $decorated;

    private ResourceExtractorInterface $extractor;

    public function __construct(
        ResourceExtractorInterface $extractor,
        ResourceMetadataFactoryInterface $decorated = null
    ) {
        $this->extractor = $extractor;
        $this->decorated = $decorated;
    }

    /** @inheritdoc */
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

        $interface_exists = interface_exists($resourceClass);
        $class_exists = class_exists($resourceClass);
        if (!($class_exists || $interface_exists)) {
            return $this->handleNotFound($parentResourceMetadata, $resourceClass);
        }

        return $this->createFromInterface(new ResourceMetadata(), $resourceClass);
    }

    /**
     * @throws ResourceClassNotFoundException
     */
    private function createFromInterface(ResourceMetadata $resourceMetadata, string $resourceClass): ResourceMetadata
    {
        $baseResourceClass = (string) preg_replace('/Interface/', '', $resourceClass);
        $metadata = [];

        $resources = $this->extractor->getResources();
        if (array_key_exists($baseResourceClass, $resources)) {
            /** @var array $metadata */
            $metadata = $resources[$baseResourceClass];
        } else {
            $this->handleNotFound($resourceMetadata, $resourceClass);
        }

        foreach (['shortName', 'description', 'iri', 'itemOperations', 'collectionOperations', 'subresourceOperations', 'graphql', 'attributes'] as $property) {
            if (!array_key_exists($property, $metadata) || (null === $metadata[$property] || null !== $resourceMetadata->{'get' . ucfirst($property)}())) {
                continue;
            }

            /** @var ResourceMetadata $resourceMetadata */
            $resourceMetadata = $resourceMetadata->{'with' . ucfirst($property)}($metadata[$property]);
        }

        return $resourceMetadata;
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
}
