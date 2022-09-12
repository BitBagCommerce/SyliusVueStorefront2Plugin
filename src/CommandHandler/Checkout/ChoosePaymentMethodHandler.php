<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\ChoosePaymentMethod;
use InvalidArgumentException;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChoosePaymentMethodHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag_sylius_graphql.choose_order_payment_method.complete';

    private OrderRepositoryInterface $orderRepository;

    private PaymentMethodRepositoryInterface $paymentMethodRepository;

    private PaymentRepositoryInterface $paymentRepository;

    private FactoryInterface $stateMachineFactory;

    private PaymentMethodChangerInterface $paymentMethodChanger;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        FactoryInterface $stateMachineFactory,
        PaymentMethodChangerInterface $paymentMethodChanger,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->paymentMethodChanger = $paymentMethodChanger;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ChoosePaymentMethod $command): OrderInterface
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $command->orderTokenValue]);

        Assert::notNull($cart, 'Cart has not been found.');

        $paymentMethodCode = $command->paymentMethodCode;
        $paymentId = $command->paymentId;

        Assert::notNull($paymentMethodCode);
        Assert::notNull($paymentId);

        if ($cart->getState() === OrderInterface::STATE_NEW) {
            $this->paymentMethodChanger->changePaymentMethod($paymentMethodCode, $paymentId, $cart);

            /** @psalm-suppress TooManyArguments */
            $this->eventDispatcher->dispatch(new GenericEvent($cart, [$command]), self::EVENT_NAME);

            return $cart;
        }

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([
            'code' => $paymentMethodCode,
        ]);
        Assert::notNull($paymentMethod, 'Payment method has not been found');

        $payment = $this->paymentRepository->findOneByOrderId($paymentId, $cart->getId());
        Assert::notNull($payment, 'Can not find payment with given identifier.');

        if ($cart->getState() === OrderInterface::STATE_CART) {
            $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

            Assert::true(
                $stateMachine->can(
                    OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT,
                ),
                'Order cannot have payment method assigned.',
            );

            $payment->setMethod($paymentMethod);
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);

            /** @psalm-suppress TooManyArguments */
            $this->eventDispatcher->dispatch(new GenericEvent($cart, [$command]), self::EVENT_NAME);

            return $cart;
        }

        throw new InvalidArgumentException('Payment method can not be set');
    }
}
