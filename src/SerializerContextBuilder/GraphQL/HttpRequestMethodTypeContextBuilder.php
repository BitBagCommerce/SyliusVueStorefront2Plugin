<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\SerializerContextBuilder\GraphQL;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\GraphQl\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

/** @experimental */
final class HttpRequestMethodTypeContextBuilder implements SerializerContextBuilderInterface
{
    private const OPERATION_ITEM_QUERY = 'item_query';

    private const OPERATION_COLLECTION_QUERY = 'collection_query';

    private const OPERATION_CREATE = 'create';

    private const OPERATION_UPDATE = 'update';

    private const OPERATION_DELETE = 'delete';

    private const DEFAULT_OPERATIONS_TO_METHODS_MAPPING = [
        self::OPERATION_ITEM_QUERY => Request::METHOD_GET,
        self::OPERATION_COLLECTION_QUERY => Request::METHOD_GET,
        self::OPERATION_CREATE => Request::METHOD_POST,
        self::OPERATION_UPDATE => Request::METHOD_PATCH,
        self::OPERATION_DELETE => Request::METHOD_DELETE,
    ];

    /** @var SerializerContextBuilderInterface */
    private $decoratedContextBuilder;

    /** @var ResourceMetadataFactoryInterface */
    private $resourceMetadataFactory;

    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
    ) {
        $this->decoratedContextBuilder = $decoratedContextBuilder;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
    }

    public function create(
        string $resourceClass,
        string $operationName,
        array $resolverContext,
        bool $normalization,
    ): array {
        $context = $this->decoratedContextBuilder->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization,
        );

        try {
            if (true === $this->isDefaultOperation($operationName)) {
                $context[ContextKeys::HTTP_REQUEST_METHOD_TYPE] = $this->getMethodTypeForDefaultOperation(
                    $operationName,
                );

                return $context;
            }

            if (false === isset($resolverContext['is_collection'])) {
                return $context;
            }

            $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);

            $availableCustomOperations = true === $resolverContext['is_collection'] ?
                $resourceMetadata->getCollectionOperations() :
                $resourceMetadata->getItemOperations();

            if (false === isset($availableCustomOperations[$operationName])) {
                return $context;
            }
            Assert::isArray($availableCustomOperations[$operationName]);

            if (false === isset($availableCustomOperations[$operationName]['method'])) {
                return $context;
            }

            $context[ContextKeys::HTTP_REQUEST_METHOD_TYPE] = strtoupper(
                (string) $availableCustomOperations[$operationName]['method'],
            );
        } catch (ResourceClassNotFoundException $exception) {
        }

        return $context;
    }

    private function isDefaultOperation(string $operationName): bool
    {
        return array_key_exists($operationName, self::DEFAULT_OPERATIONS_TO_METHODS_MAPPING);
    }

    private function getMethodTypeForDefaultOperation(string $operationName): string
    {
        return self::DEFAULT_OPERATIONS_TO_METHODS_MAPPING[$operationName];
    }
}
