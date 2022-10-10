<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

/** @experimental */
class AddItemToCart implements OrderTokenValueAwareInterface
{
    /** @psalm-suppress   */
    public ?string $orderTokenValue;

    public string $productVariant;

    public int $quantity;

    public function __construct(
        string $productVariant,
        int $quantity,
        string $orderTokenValue,
    ) {
        $this->productVariant = $productVariant;
        $this->quantity = $quantity;
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }
}
