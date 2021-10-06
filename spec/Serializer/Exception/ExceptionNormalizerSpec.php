<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\Serializer\Exception;

use BitBag\SyliusGraphqlPlugin\Serializer\Exception\ExceptionNormalizer;
use Exception;
use GraphQL\Error\Error;
use PhpSpec\ObjectBehavior;

final class ExceptionNormalizerSpec extends ObjectBehavior
{
    /**
     * @param Exception|mixed $object
     *
     * @throws \Throwable
     */
    function it_normalizes_error(): void
    {
        $errorMessage = "Some error message";

        $e = new \InvalidArgumentException($errorMessage);
        $object = new Error("Message", null, null, [], null, $e);

        $error = [
            'message' => "Internal server error",
            'extensions' => [
                'category' => "internal"
            ]
        ];

        $error['extensions']['message'] = $errorMessage;

        $this->normalize($object)->shouldReturn($error);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ExceptionNormalizer::class);
    }

    function it_checks_if_it_supports_normalization_without_previous(Error $data, \InvalidArgumentException $exception): void
    {
        $this->supportsNormalization($data)->shouldReturn(false);
    }

    function it_checks_if_it_supports_normalization(Error $data): void
    {
        $e = new \InvalidArgumentException();
        $error = new Error("Message", null, null, [], null, $e);

        $this->supportsNormalization($error)->shouldReturn(true);
    }
}
