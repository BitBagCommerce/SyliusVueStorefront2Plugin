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
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;

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
            $context[ContextKeys::LOCALE_CODE] = $this->localeContext->getLocaleCode();
            if (is_a($resourceClass, ProductAttributeValueInterface::class, true)) {
                if (isset($resolverContext['args']['localeCode'])) {
                    $context[ContextKeys::LOCALE_CODE] = $resolverContext['args']['localeCode'];
                }
            } else {
                if (isset($resolverContext['args']['translations_locale'])) {
                    $context[ContextKeys::LOCALE_CODE] = $resolverContext['args']['translations_locale'];
                }
            }
        } catch (LocaleNotFoundException $exception) {
        }

        return $context;
    }
}
