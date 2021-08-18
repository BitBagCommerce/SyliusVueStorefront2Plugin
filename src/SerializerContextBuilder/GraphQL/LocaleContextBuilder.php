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
        LocaleContextInterface $localeContext
    )
    {
        $this->decoratedContextBuilder = $decoratedContextBuilder;
        $this->localeContext = $localeContext;
    }

    public function create(
        string $resourceClass,
        string $operationName,
        array $resolverContext,
        bool $normalization
    ): array
    {
        $context = $this->decoratedContextBuilder->create(
            $resourceClass,
            $operationName,
            $resolverContext,
            $normalization
        );

        try {
            $context[ContextKeys::LOCALE_CODE] = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException $exception) {
        }

        return $context;
    }
}
