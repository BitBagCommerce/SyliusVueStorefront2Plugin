<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Cart;

final class AddItemsToCart
{
    public string $orderTokenValue;

    public array $cartItems;

    public function __construct(string $orderTokenValue, array $cartItems)
    {
        $this->orderTokenValue = $orderTokenValue;
        $this->cartItems = $cartItems;
    }

    public function getOrderTokenValue(): string
    {
        return $this->orderTokenValue;
    }

    public function getCartItems(): array
    {
        return $this->cartItems;
    }
}
