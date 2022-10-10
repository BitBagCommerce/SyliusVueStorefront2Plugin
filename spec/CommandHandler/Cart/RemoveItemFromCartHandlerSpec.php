<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\CommandHandler\Cart;

use BitBag\SyliusVueStorefront2Plugin\Command\Cart\RemoveItemFromCart;
use BitBag\SyliusVueStorefront2Plugin\CommandHandler\Cart\RemoveItemFromCartHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\InvalidArgumentException;

final class RemoveItemFromCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderModifierInterface $orderModifier,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $this->beConstructedWith(
            $orderItemRepository,
            $orderModifier,
            $eventDispatcher
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveItemFromCartHandler::class);
    }

    function it_is_invokable(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderModifierInterface $orderModifier,
        OrderItemInterface $orderItem,
        OrderInterface $cart,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $tokenValue = 'token';
        $removeItemFromCart = new RemoveItemFromCart($tokenValue, '222');

        $orderItemRepository->findOneByIdAndCartTokenValue(
            $removeItemFromCart->itemId,
            $removeItemFromCart->orderTokenValue,
        )->willReturn($orderItem);

        $orderItem->getOrder()->willReturn($cart);
        $cart->getTokenValue()->willReturn($tokenValue);

        $orderModifier->removeFromOrder($cart->getWrappedObject(), $orderItem->getWrappedObject())->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), RemoveItemFromCartHandler::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($removeItemFromCart);
    }

    function it_throws_an_exception_when_cart_is_not_found(
        OrderItemRepositoryInterface $orderItemRepository
    ): void {
        $tokenValue = 'token';
        $removeItemFromCart = new RemoveItemFromCart($tokenValue, '222');

        $orderItemRepository->findOneByIdAndCartTokenValue(
            $removeItemFromCart->itemId,
            $removeItemFromCart->orderTokenValue,
        )->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$removeItemFromCart])
        ;
    }

    function it_throws_an_exception_when_tokens_mismatch(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemInterface $orderItem,
        OrderInterface $cart
    ): void {
        $tokenValue = 'token';
        $removeItemFromCart = new RemoveItemFromCart($tokenValue, '222');

        $orderItemRepository->findOneByIdAndCartTokenValue(
            $removeItemFromCart->itemId,
            $removeItemFromCart->orderTokenValue,
        )->willReturn($orderItem);

        $orderItem->getOrder()->willReturn($cart);
        $cart->getTokenValue()->willReturn($removeItemFromCart);

        $this->shouldThrow(\Error::class)
            ->during('__invoke', [$removeItemFromCart])
        ;
    }
}
