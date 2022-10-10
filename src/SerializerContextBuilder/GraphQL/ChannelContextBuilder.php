<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\SerializerContextBuilder\GraphQL;

use ApiPlatform\Core\GraphQl\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;

/** @experimental */
final class ChannelContextBuilder implements SerializerContextBuilderInterface
{
    /** @var SerializerContextBuilderInterface */
    private $decoratedContextBuilder;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        ChannelContextInterface $channelContext,
    ) {
        $this->decoratedContextBuilder = $decoratedContextBuilder;
        $this->channelContext = $channelContext;
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
            $context[ContextKeys::CHANNEL] = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
        }

        return $context;
    }
}
