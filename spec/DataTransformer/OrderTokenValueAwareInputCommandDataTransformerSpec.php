<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\DataTransformer;

use BitBag\SyliusGraphqlPlugin\DataTransformer\OrderTokenValueAwareInputCommandDataTransformer;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderTokenValueAwareInputCommandDataTransformerSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderTokenValueAwareInputCommandDataTransformer::class);
    }

    function it_transforms_object_to_populate(
        OrderTokenValueAwareInterface $object,
        OrderInterface $cart,
    ): void {
        $context = [
            'object_to_populate' => $cart,
        ];
        $tokenValue = 'token';

        $cart = $context['object_to_populate'];
        $cart->getTokenValue()->willReturn($tokenValue);

        $object->setOrderTokenValue($tokenValue)->shouldBeCalled();

        $this->transform($object, '', $context)->shouldReturn($object);
    }

    function it_transforms_object_with_property(
        OrderTokenValueAwareInterface $object,
    ): void {
        $context = [];
        $tokenValue = 'token';

        $object->getOrderTokenValue()->willReturn($tokenValue);
        $object->setOrderTokenValue($tokenValue)->shouldBeCalled();

        $this->transform($object, '', $context)->shouldReturn($object);
    }

    function it_supports_transformation(
        OrderTokenValueAwareInterface $object,
    ): void {
        $this->supportsTransformation($object)->shouldReturn(true);
    }
}
