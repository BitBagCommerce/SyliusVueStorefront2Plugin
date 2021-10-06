<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\DataTransformer;

use BitBag\SyliusGraphqlPlugin\DataTransformer\SubresourceIdAwareCommandDataTransformer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;


final class SubresourceIdAwareCommandDataTransformerSpec extends ObjectBehavior
{

    function let(RequestStack $requestStack): void
    {
        $this->beConstructedWith($requestStack);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SubresourceIdAwareCommandDataTransformer::class);
    }

    function it_transforms(
        SubresourceIdAwareInterface $object,
        RequestStack $requestStack
    ): void
    {
        $attributeKey = "key";
        $subresourceId = "id";

        $requestAttributes = [
            $attributeKey => $subresourceId
        ];

        $request = new Request([], [], $requestAttributes);

        $requestStack->getCurrentRequest()->willReturn($request);

        $object->getSubresourceIdAttributeKey()->willReturn($attributeKey);
        $object->getSubresourceId()->willReturn($subresourceId);

        $this->transform($object, "")->shouldReturn($object);
    }

    function it_throws_an_exception(
        SubresourceIdAwareInterface $object,
        RequestStack $requestStack,
        Request $request
    ): void
    {
        $requestStack->getCurrentRequest()->willReturn($request);

        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('transform', [null, ""]);
    }

    function it_checks_if_it_supports_transformation(SubresourceIdAwareInterface $object): void
    {
        $this->supportsTransformation($object)->shouldReturn(true);
    }
}
