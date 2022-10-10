<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\CommandHandler\Checkout;

use BitBag\SyliusVueStorefront2Plugin\Command\Checkout\ChooseShippingMethod;
use BitBag\SyliusVueStorefront2Plugin\CommandHandler\Checkout\ChooseShippingMethodHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\InvalidArgumentException;

final class ChooseShippingMethodHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $shippingMethodRepository,
            $shipmentRepository,
            $eligibilityChecker,
            $stateMachineFactory,
            $eventDispatcher,
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ChooseShippingMethodHandler::class);
    }

    function it_is_invokable(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod,
        StateMachineInterface $stateMachine,
        ShipmentInterface $shipment,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('token', 'ups', 'shipmentId');
        $orderRepository->findOneBy(['tokenValue' => $chooseShippingMethod->orderTokenValue])->willReturn($cart);
        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING)->willReturn(true);

        $shippingMethodRepository->findOneBy([
            'code' => $chooseShippingMethod->shippingMethodCode,
        ])->willReturn($shippingMethod);

        $cartId = 1;
        $cart->getId()->willReturn($cartId);
        $shipmentRepository->findOneByOrderId($chooseShippingMethod->shipmentId, $cartId)->willReturn($shipment);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $shipment->setMethod($shippingMethod)->shouldBeCalled();
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING)->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), ChooseShippingMethodHandler::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($chooseShippingMethod);
    }

    function it_throws_an_exception_on_null_cart(
        OrderRepositoryInterface $orderRepository
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('token', 'ups', 'shipmentId');
        $orderRepository->findOneBy(['tokenValue' => $chooseShippingMethod->orderTokenValue])->willReturn(null);
        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_on_null_shipping_method(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        FactoryInterface $stateMachineFactory,
        OrderInterface $cart,
        StateMachineInterface $stateMachine
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('token', 'ups', 'shipmentId');

        $orderRepository->findOneBy(['tokenValue' => $chooseShippingMethod->orderTokenValue])->willReturn($cart);

        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING)->willReturn(true);

        $shippingMethodRepository->findOneBy([
            'code' => $chooseShippingMethod->shippingMethodCode,
        ])->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_on_null_shipment(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        FactoryInterface $stateMachineFactory,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod,
        StateMachineInterface $stateMachine
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('token', 'ups', 'shipmentId');
        $orderRepository->findOneBy(['tokenValue' => $chooseShippingMethod->orderTokenValue])->willReturn($cart);

        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING)->willReturn(true);

        $shippingMethodRepository->findOneBy([
            'code' => $chooseShippingMethod->shippingMethodCode,
        ])->willReturn($shippingMethod);

        $cartId = 1;
        $cart->getId()->willReturn($cartId);
        $shipmentRepository->findOneByOrderId($chooseShippingMethod->shipmentId, $cartId)->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }
}
