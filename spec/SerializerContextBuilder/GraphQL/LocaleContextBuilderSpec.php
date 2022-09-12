<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\SerializerContextBuilder\GraphQL;

use ApiPlatform\Core\GraphQl\Serializer\SerializerContextBuilderInterface;
use BitBag\SyliusGraphqlPlugin\SerializerContextBuilder\GraphQL\LocaleContextBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class LocaleContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        LocaleContextInterface $localeContext,
    ): void {
        $this->beConstructedWith($decoratedContextBuilder, $localeContext);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(LocaleContextBuilder::class);
    }

    function it_creates(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        LocaleContextInterface $localeContext,
    ): void {
        $resourceClass = 'Class/Name';
        $operationName = 'operation_name';
        $resolverContext = [];
        $normalization = true;

        $context = [
            ContextKeys::CHANNEL => 'channelName',
            ContextKeys::LOCALE_CODE => 'en_US',
        ];
        $decoratedContextBuilder->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization,
        )->willReturn($context);

        $localeContext->getLocaleCode()->willReturn('en_US');

        $this->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization,
        )->shouldReturn($context);
    }
}
