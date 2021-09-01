<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Resolver;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Webmozart\Assert\Assert;

final class OrderAddressStateResolver implements OrderAddressStateResolverInterface
{
    private StateMachineFactoryInterface $stateMachineFactory;

    public function __construct(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function resolve(OrderInterface $order): void
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS),
            sprintf('Order with %s token cannot be addressed.', $order->getTokenValue())
        );

        if ($order->getBillingAddress() !== null || $order->getShippingAddress() !== null) {
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);
        }
    }
}
