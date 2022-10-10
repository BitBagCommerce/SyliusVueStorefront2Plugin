<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Serializer\Exception;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ExceptionNormalizer implements NormalizerInterface
{
    /**
     * @param Exception|mixed $object
     * @param string|null $format
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, Exception::class);
        $exception = $object->getPrevious() ?? $object;
        $error = FormattedError::createFromException($object);

        Assert::isArray($error['extensions']);
        $error['extensions']['message'] = $exception->getMessage();

        return $error;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Error;
    }
}
