<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\CommandHandler\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\ChoosePaymentMethod;
use BitBag\SyliusGraphqlPlugin\CommandHandler\Checkout\ChoosePaymentMethodHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\InvalidArgumentException;


final class ChoosePaymentMethodHandlerSpec extends ObjectBehavior
{

    function let(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        FactoryInterface $stateMachineFactory,
        PaymentMethodChangerInterface $paymentMethodChanger,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->beConstructedWith(
            $orderRepository,
            $paymentMethodRepository,
            $paymentRepository,
            $stateMachineFactory,
            $paymentMethodChanger,
            $eventDispatcher
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ChoosePaymentMethodHandler::class);
    }

    function it_is_invokable(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        FactoryInterface $stateMachineFactory,
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderInterface $cart,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        StateMachineInterface $stateMachine,
        EventDispatcherInterface $eventDispatcher
    ): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod("token", "cash", "paymentId");
        $orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderTokenValue])->willReturn($cart);

        $paymentMethodCode = $choosePaymentMethod->paymentMethodCode;
        $paymentId = $choosePaymentMethod->paymentId;

        $cart->getState()->willReturn(OrderInterface::STATE_CART);
        $paymentMethodChanger->changePaymentMethod($paymentMethodCode, $paymentId, $cart)->shouldNotBeCalled();

        $paymentMethodRepository->findOneBy([
            'code' => $paymentMethodCode,
        ])->willReturn($paymentMethod);

        $cartId = 1;
        $cart->getId()->willReturn($cartId);
        $paymentRepository->findOneByOrderId($paymentId, $cartId)->willReturn($payment);

        $cart->getState()->willReturn(OrderInterface::STATE_CART);
        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT)->willReturn(true);
        $payment->setMethod($paymentMethod)->shouldBeCalled();
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT)->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), ChoosePaymentMethodHandler::EVENT_NAME)->willReturn(Argument::any());

        $this->__invoke($choosePaymentMethod)->shouldReturn($cart);
    }

    function it_throws_exception_when_payment_cannot_be_assigned(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        FactoryInterface $stateMachineFactory,
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderInterface $cart,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
        StateMachineInterface $stateMachine
    ): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod("token", "cash", "paymentId");
        $orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderTokenValue])->willReturn($cart);

        $paymentMethodCode = $choosePaymentMethod->paymentMethodCode;
        $paymentId = $choosePaymentMethod->paymentId;

        $cart->getState()->willReturn(OrderInterface::STATE_CART);
        if ($cart->getState() === OrderInterface::STATE_NEW) {
            $paymentMethodChanger->changePaymentMethod($paymentMethodCode, $paymentId, $cart);
        }

        $paymentMethodRepository->findOneBy([
            'code' => $paymentMethodCode,
        ])->willReturn($paymentMethod);

        $cartId = 1;
        $cart->getId()->willReturn($cartId);
        $paymentRepository->findOneByOrderId($paymentId, $cartId)->willReturn($payment);

        $cart->getState()->willReturn(OrderInterface::STATE_CART);
        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT)->willReturn(false);

        $this->shouldThrow(\Webmozart\Assert\InvalidArgumentException::class)
            ->during('__invoke', [$choosePaymentMethod]);
    }

    function it_throws_exception_when_cannot_find_payment(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderInterface $cart,
        PaymentMethodInterface $paymentMethod
    ): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod("token", "cash", "paymentId");
        $orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderTokenValue])->willReturn($cart);

//        Assert::notNull($cart, 'Cart has not been found.');

        $paymentMethodCode = $choosePaymentMethod->paymentMethodCode;
        $paymentId = $choosePaymentMethod->paymentId;

        $cart->getState()->willReturn(OrderInterface::STATE_CART);
        $paymentMethodChanger->changePaymentMethod($paymentMethodCode, $paymentId, $cart)->shouldNotBeCalled();

        $paymentMethodRepository->findOneBy([
            'code' => $paymentMethodCode,
        ])->willReturn($paymentMethod);

        $cartId = 1;
        $cart->getId()->willReturn($cartId);
        $paymentRepository->findOneByOrderId($paymentId, $cartId)->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$choosePaymentMethod]);
    }

    function it_throws_exception_when_cannot_find_payment_method(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderInterface $cart
    ): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod("token", "cash", "paymentId");
        $orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderTokenValue])->willReturn($cart);

//        Assert::notNull($cart, 'Cart has not been found.');

        $paymentMethodCode = $choosePaymentMethod->paymentMethodCode;
        $paymentId = $choosePaymentMethod->paymentId;

        if ($cart->getState() === OrderInterface::STATE_NEW) {
            $paymentMethodChanger->changePaymentMethod($paymentMethodCode, $paymentId, $cart);
        }

        $paymentMethodRepository->findOneBy([
            'code' => $paymentMethodCode,
        ])->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$choosePaymentMethod]);
    }

    function it_throws_exception_when_cannot_find_cart(
        OrderRepositoryInterface $orderRepository
    ): void
    {
        $choosePaymentMethod = new ChoosePaymentMethod("token", "cash", "paymentId");
        $orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderTokenValue])->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$choosePaymentMethod]);
    }
}
