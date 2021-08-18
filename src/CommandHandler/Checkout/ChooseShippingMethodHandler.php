<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\ChooseShippingMethod;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChooseShippingMethodHandler implements MessageHandlerInterface
{
    private OrderRepositoryInterface $orderRepository;

    private ShippingMethodRepositoryInterface $shippingMethodRepository;

    private ShipmentRepositoryInterface $shipmentRepository;

    private ShippingMethodEligibilityCheckerInterface $eligibilityChecker;

    private FactoryInterface $stateMachineFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory
    )
    {
        $this->orderRepository = $orderRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->eligibilityChecker = $eligibilityChecker;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function __invoke(ChooseShippingMethod $chooseShippingMethod): OrderInterface
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $chooseShippingMethod->orderTokenValue]);

        Assert::notNull($cart, 'Cart has not been found.');

        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING),
            'Order cannot have shipment method assigned.'
        );

        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy([
            'code' => $chooseShippingMethod->shippingMethodCode,
        ]);
        Assert::notNull($shippingMethod, 'Shipping method has not been found');

        $shipment = $this->shipmentRepository->findOneByOrderId($chooseShippingMethod->shipmentId, $cart->getId());
        Assert::notNull($shipment, 'Can not find shipment with given identifier.');

        Assert::true(
            $this->eligibilityChecker->isEligible($shipment, $shippingMethod),
            'Given shipment is not eligible for provided shipping method.'
        );

        $shipment->setMethod($shippingMethod);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        return $cart;
    }
}
