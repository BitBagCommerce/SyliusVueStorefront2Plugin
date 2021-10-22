<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Cart;

use ApiPlatform\Core\Api\IriConverterInterface;
use BitBag\SyliusGraphqlPlugin\Command\Cart\AddItemToCart;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddItemToCartHandler implements MessageHandlerInterface
{
    private OrderRepositoryInterface $orderRepository;

    private OrderModifierInterface $orderModifier;

    private CartItemFactoryInterface $cartItemFactory;

    private OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    private AvailabilityCheckerInterface $availabilityChecker;

    private IriConverterInterface $iriConverter;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderModifierInterface $orderModifier,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        AvailabilityCheckerInterface $availabilityChecker,
        IriConverterInterface $iriConverter
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderModifier = $orderModifier;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->availabilityChecker = $availabilityChecker;
        $this->iriConverter = $iriConverter;
    }

    public function __invoke(AddItemToCart $addItemToCart): OrderInterface
    {
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->iriConverter->getItemFromIri($addItemToCart->productVariant);

        Assert::notNull($productVariant);
        Assert::notNull($addItemToCart->orderTokenValue);

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($addItemToCart->orderTokenValue);

        Assert::notNull($cart);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createNew();
        $cartItem->setVariant($productVariant);

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $productVariant,
            $addItemToCart->quantity + $this->getExistingCartItemQuantityFromCart($cart, $cartItem)
        );

        Assert::true($isStockSufficient, 'There are no that many items on stock.');

        $this->orderItemQuantityModifier->modify($cartItem, $addItemToCart->quantity);
        $this->orderModifier->addToOrder($cart, $cartItem);

        return $cart;
    }

    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): int
    {
        foreach ($cart->getItems() as $existingCartItem) {
            if ($existingCartItem->equals($cartItem)) {
                return $existingCartItem->getQuantity();
            }
        }

        return 0;
    }
}
