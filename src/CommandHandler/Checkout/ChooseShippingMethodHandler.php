<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Checkout;

use BitBag\SyliusVueStorefront2Plugin\Command\Checkout\ChooseShippingMethod;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChooseShippingMethodHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag.sylius_vue_storefront2.choose_order_shipping_method.complete';

    private OrderRepositoryInterface $orderRepository;

    private ShippingMethodRepositoryInterface $shippingMethodRepository;

    private ShipmentRepositoryInterface $shipmentRepository;

    private ShippingMethodEligibilityCheckerInterface $eligibilityChecker;

    private FactoryInterface $stateMachineFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->orderRepository = $orderRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->eligibilityChecker = $eligibilityChecker;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ChooseShippingMethod $command): OrderInterface
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $command->orderTokenValue]);

        Assert::notNull($cart, 'Cart has not been found.');

        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING),
            'Order cannot have shipment method assigned.',
        );

        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy([
            'code' => $command->shippingMethodCode,
        ]);
        Assert::notNull($shippingMethod, 'Shipping method has not been found');

        $shipment = $this->shipmentRepository->findOneByOrderId($command->shipmentId, $cart->getId());
        Assert::notNull($shipment, 'Can not find shipment with given identifier.');

        Assert::true(
            $this->eligibilityChecker->isEligible($shipment, $shippingMethod),
            'Given shipment is not eligible for provided shipping method.',
        );

        $shipment->setMethod($shippingMethod);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        /** @psalm-suppress TooManyArguments */
        $this->eventDispatcher->dispatch(new GenericEvent($cart, [$command]), self::EVENT_NAME);

        return $cart;
    }
}
