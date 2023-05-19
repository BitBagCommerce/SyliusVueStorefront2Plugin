<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\CommandHandler\Trait;

use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface as ModelOrderInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Webmozart\Assert\Assert;

trait AddProductVariantToCartTrait
{
    private OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    private OrderModifierInterface $orderModifier;

    private CartItemFactoryInterface $cartItemFactory;

    private AvailabilityCheckerInterface $availabilityChecker;

    public function addProductVariantToCart(
        ProductVariantInterface $productVariant,
        int $quantity,
        OrderInterface $cart,
    ): void {
        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createNew();
        $cartItem->setVariant($productVariant);

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $productVariant,
            $quantity + $this->getExistingCartItemQuantityFromCart($cart, $cartItem),
        );

        Assert::true($isStockSufficient, 'There are no that many items on stock.');

        $this->orderItemQuantityModifier->modify($cartItem, $quantity);
        $this->orderModifier->addToOrder($cart, $cartItem);
    }

    private function getExistingCartItemQuantityFromCart(ModelOrderInterface $cart, OrderItemInterface $cartItem): int
    {
        foreach ($cart->getItems() as $existingCartItem) {
            if ($existingCartItem->equals($cartItem)) {
                return $existingCartItem->getQuantity();
            }
        }

        return 0;
    }
}
