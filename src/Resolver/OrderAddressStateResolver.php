<?php


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
