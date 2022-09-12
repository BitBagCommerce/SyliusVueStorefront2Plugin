<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\SerializerContextBuilder\GraphQL;

use ApiPlatform\Core\GraphQl\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;

/** @experimental */
final class LocaleContextBuilder implements SerializerContextBuilderInterface
{
    /** @var SerializerContextBuilderInterface */
    private $decoratedContextBuilder;

    /** @var LocaleContextInterface */
    private $localeContext;

    public function __construct(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        LocaleContextInterface $localeContext,
    ) {
        $this->decoratedContextBuilder = $decoratedContextBuilder;
        $this->localeContext = $localeContext;
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
            if (isset($resolverContext['args']['translations_locale'])) {
                $context[ContextKeys::LOCALE_CODE] = $resolverContext['args']['translations_locale'];

                return $context;
            }

            $context[ContextKeys::LOCALE_CODE] = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException $exception) {
        }

        return $context;
    }
}
