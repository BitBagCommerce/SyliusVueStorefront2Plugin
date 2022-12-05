<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Resolver;

use BitBag\SyliusVueStorefront2Plugin\Resolver\OrderAddressStateResolver;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Webmozart\Assert\InvalidArgumentException;

final class OrderAddressStateResolverSpec extends ObjectBehavior
{
    public function let(StateMachineFactoryInterface $stateMachineFactory): void
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderAddressStateResolver::class);
    }

    public function it_resolves(
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $token = 'token';
        $order->getTokenValue()->willReturn($token);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);

        $order->getBillingAddress()->shouldBeCalled();

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->resolve($order);
    }

    public function it_throws_an_exception_on_resolving(
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);

        $token = 'token';
        $order->getTokenValue()->willReturn($token);

        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(false);

        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('resolve', [$order])
        ;
    }
}
