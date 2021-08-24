<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\SerializerContextBuilder\GraphQL;

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
        ChannelContextInterface $channelContext
    ) {
        $this->decoratedContextBuilder = $decoratedContextBuilder;
        $this->channelContext = $channelContext;
    }

    public function create(
        string $resourceClass,
        string $operationName,
        array $resolverContext,
        bool $normalization
    ): array {
        $context = $this->decoratedContextBuilder->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization
        );

        try {
            $context[ContextKeys::CHANNEL] = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
        }

        return $context;
    }
}
