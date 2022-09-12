<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\SerializerContextBuilder\GraphQL;

use ApiPlatform\Core\GraphQl\Serializer\SerializerContextBuilderInterface;
use BitBag\SyliusGraphqlPlugin\SerializerContextBuilder\GraphQL\ChannelContextBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext,
    ): void {
        $this->beConstructedWith($decoratedContextBuilder, $channelContext);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ChannelContextBuilder::class);
    }

    function it_creates_context(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
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
            $normalization,
        )->willReturn($context);

        $channelContext->getChannel()->willReturn($channel);

        $context = [
            ContextKeys::CHANNEL => $channel,
        ];

        $this->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization,
        )->shouldReturn($context);
    }
}
