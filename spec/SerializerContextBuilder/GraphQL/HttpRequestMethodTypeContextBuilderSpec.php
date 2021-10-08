<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\SerializerContextBuilder\GraphQL;

use ApiPlatform\Core\GraphQl\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use BitBag\SyliusGraphqlPlugin\SerializerContextBuilder\GraphQL\HttpRequestMethodTypeContextBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Symfony\Component\HttpFoundation\Request;

final class HttpRequestMethodTypeContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ): void {
        $this->beConstructedWith($decoratedContextBuilder, $resourceMetadataFactory);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(HttpRequestMethodTypeContextBuilder::class);
    }

    function it_creates_context_for_default_operation(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ): void {
        $context = [
            ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
        ];
        $resourceClass = 'Class/Name';
        $operationName = 'item_query';
        $resolverContext = [];
        $normalization = true;

        $decoratedContextBuilder->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization
        )->willReturn($context);

        $this->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization
        )->shouldReturn($context);
    }

    function it_creates_context_for_other_operation(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ): void {
        $context = [];
        $resourceClass = 'Class/Name';
        $operationName = 'custom_operation';
        $resolverContext = [
            'is_collection' => false,
        ];
        $normalization = true;

        $decoratedContextBuilder->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization
        )->willReturn($context);

        $resourceMetadata = new ResourceMetadata(
            'name',
            'description',
            'some/iri',
            [],
            []
        );

        $resourceMetadataFactory->create($resourceClass)->willReturn($resourceMetadata);

        $this->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization
        )->shouldReturn($context);
    }
}
