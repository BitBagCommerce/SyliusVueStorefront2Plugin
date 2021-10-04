<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Serializer\Exception;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ExceptionNormalizer implements NormalizerInterface
{
    /**
     * @param Exception|mixed $object
     *
     * @throws \Throwable
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, Exception::class);
        $exception = $object->getPrevious();
        $error = FormattedError::createFromException($object);

        Assert::isArray($error['extensions']);
        Assert::notNull($exception);
        $error['extensions']['message'] = $exception->getMessage();

        return $error;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Error && $data->getPrevious() instanceof \InvalidArgumentException;
    }
}
