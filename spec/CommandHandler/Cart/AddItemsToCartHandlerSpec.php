<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\CommandHandler\Cart;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusVueStorefront2Plugin\Command\Cart\AddItemsToCart;
use BitBag\SyliusVueStorefront2Plugin\CommandHandler\Cart\AddItemsToCartHandler;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Webmozart\Assert\InvalidArgumentException;

final class AddItemsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        OrderRepositoryInterface $orderRepository,
        OrderModifierInterface $orderModifier,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        AvailabilityCheckerInterface $availabilityChecker,
        IriConverterInterface $iriConverter,
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $orderModifier,
            $cartItemFactory,
            $orderItemQuantityModifier,
            $availabilityChecker,
            $iriConverter,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddItemsToCartHandler::class);
    }

    public function it_is_invokable(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        IriConverterInterface $iriConverter,
        ProductVariantInterface $productVariant,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemInterface $orderItem,
        AvailabilityCheckerInterface $availabilityChecker,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderModifierInterface $orderModifier,
    ): void {
        $quantity = 1;
        $cartItem = [
            'productVariant' => 'productVariantIri',
            'quantity' => $quantity,
        ];
        $addItemsToCart = new AddItemsToCart('orderTokenValue', [$cartItem]);

        $orderRepository->findCartByTokenValue($addItemsToCart->getOrderTokenValue())->willReturn($order);
        $iriConverter->getItemFromIri($cartItem['productVariant'])->willReturn($productVariant);

        $cartItemFactory->createNew()->willReturn($orderItem);
        $orderItem->setVariant($productVariant)->shouldBeCalled();

        $order->getItems()->willReturn(new ArrayCollection());

        $availabilityChecker->isStockSufficient($productVariant, $quantity)->willReturn(true);

        $orderItemQuantityModifier->modify($orderItem, $quantity)->shouldBeCalled();
        $orderModifier->addToOrder($order, $orderItem)->shouldBeCalled();

        $this->__invoke($addItemsToCart)->shouldReturn($order);
    }

    public function it_is_invokable_for_empty_cart_items(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        OrderModifierInterface $orderModifier,
    ): void {
        $addItemsToCart = new AddItemsToCart('orderTokenValue', []);

        $orderRepository->findCartByTokenValue($addItemsToCart->getOrderTokenValue())->willReturn($order);

        $this->__invoke($addItemsToCart)->shouldReturn($order);

        $orderModifier->addToOrder($order, Argument::any())->shouldNotHaveBeenCalled();
    }

    public function it_throws_an_exception_when_cannot_find_order(
        OrderRepositoryInterface $orderRepository,
    ): void {
        $cartItem = [
            'quantity' => 1,
            'productVariant' => 'productVariantIri',
        ];
        $addItemsToCart = new AddItemsToCart('orderTokenValue', [$cartItem]);

        $orderRepository->findCartByTokenValue($addItemsToCart->getOrderTokenValue())->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addItemsToCart])
        ;
    }

    public function it_throws_an_exception_when_cart_item_does_not_have_product_variant(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $cartItem = [
            'quantity' => 1,
        ];
        $addItemsToCart = new AddItemsToCart('orderTokenValue', [$cartItem]);

        $orderRepository->findCartByTokenValue($addItemsToCart->getOrderTokenValue())->willReturn($order);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addItemsToCart])
        ;
    }

    public function it_throws_an_exception_when_cart_item_does_not_have_quantity(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        IriConverterInterface $iriConverter,
        ProductVariantInterface $productVariant,
    ): void {
        $cartItem = [
            'productVariant' => 'productVariantIri',
        ];
        $addItemsToCart = new AddItemsToCart('orderTokenValue', [$cartItem]);

        $orderRepository->findCartByTokenValue($addItemsToCart->getOrderTokenValue())->willReturn($order);
        $iriConverter->getItemFromIri($cartItem['productVariant'])->willReturn($productVariant);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addItemsToCart])
        ;
    }

    public function it_throws_an_exception_when_cannot_find_product_variant(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        IriConverterInterface $iriConverter,
    ): void {
        $cartItem = [
            'productVariant' => 'productVariantIri',
            'quantity' => 1,
        ];
        $addItemsToCart = new AddItemsToCart('orderTokenValue', [$cartItem]);

        $orderRepository->findCartByTokenValue($addItemsToCart->getOrderTokenValue())->willReturn($order);
        $iriConverter->getItemFromIri($cartItem['productVariant'])->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addItemsToCart])
        ;
    }

    public function it_throws_an_exception_when_quantity_is_not_integer(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        IriConverterInterface $iriConverter,
        ProductVariantInterface $productVariant,
    ): void {
        $cartItem = [
            'productVariant' => 'productVariantIri',
            'quantity' => 'test',
        ];
        $addItemsToCart = new AddItemsToCart('orderTokenValue', [$cartItem]);

        $orderRepository->findCartByTokenValue($addItemsToCart->getOrderTokenValue())->willReturn($order);
        $iriConverter->getItemFromIri($cartItem['productVariant'])->willReturn($productVariant);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addItemsToCart])
        ;
    }

    public function it_throws_an_exception_when_there_are_no_that_many_items_on_stock(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        IriConverterInterface $iriConverter,
        ProductVariantInterface $productVariant,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemInterface $orderItem,
        AvailabilityCheckerInterface $availabilityChecker,
    ): void {
        $quantity = 1;
        $cartItem = [
            'productVariant' => 'productVariantIri',
            'quantity' => $quantity,
        ];
        $addItemsToCart = new AddItemsToCart('orderTokenValue', [$cartItem]);

        $orderRepository->findCartByTokenValue($addItemsToCart->getOrderTokenValue())->willReturn($order);
        $iriConverter->getItemFromIri($cartItem['productVariant'])->willReturn($productVariant);

        $cartItemFactory->createNew()->willReturn($orderItem);
        $orderItem->setVariant($productVariant)->shouldBeCalled();

        $order->getItems()->willReturn(new ArrayCollection());

        $availabilityChecker->isStockSufficient($productVariant, $quantity)->willReturn(false);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addItemsToCart])
        ;
    }
}
