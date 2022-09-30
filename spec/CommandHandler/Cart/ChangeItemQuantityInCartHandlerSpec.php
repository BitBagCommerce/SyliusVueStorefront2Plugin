<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\CommandHandler\Cart;

use BitBag\SyliusGraphqlPlugin\Command\Cart\ChangeItemQuantityInCart;
use BitBag\SyliusGraphqlPlugin\CommandHandler\Cart\ChangeItemQuantityInCartHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChangeItemQuantityInCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        EventDispatcherInterface $eventDispatcher,
        AvailabilityCheckerInterface $availabilityChecker
    ): void {
        $this->beConstructedWith(
            $orderItemRepository,
            $orderItemQuantityModifier,
            $orderProcessor,
            $eventDispatcher,
            $availabilityChecker
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ChangeItemQuantityInCartHandler::class);
    }

    function it_is_invokable(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        OrderItemInterface $orderItem,
        OrderInterface $cart,
        EventDispatcherInterface $eventDispatcher,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantInterface $variant
    ): void {
        $orderToken = 'token';
        $command = new ChangeItemQuantityInCart(10, 'itemId', $orderToken);

        $orderItemRepository->findOneByIdAndCartTokenValue(
            $command->orderItemId,
            $command->orderTokenValue,
        )->willReturn($orderItem);

        $orderItem->getOrder()->willReturn($cart);
        $cart->getTokenValue()->willReturn($orderToken);

        $orderItem->getVariant()->willReturn($variant);
        $availabilityChecker->isStockSufficient($variant, $command->quantity)->willReturn(true);

        $orderItemQuantityModifier->modify($orderItem->getWrappedObject(), $command->quantity)->shouldBeCalled();
        $orderProcessor->process($cart->getWrappedObject())->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), ChangeItemQuantityInCartHandler::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($command);
    }

    function it_throws_an_exception_if_token_is_invalid(
        OrderItemRepositoryInterface $orderItemRepository
    ): void {
        $orderToken = 'token';
        $command = new ChangeItemQuantityInCart(10, 'itemId', $orderToken);

        $orderItemRepository->findOneByIdAndCartTokenValue(
            $command->orderItemId,
            $command->orderTokenValue,
        )->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command])
        ;
    }
}
