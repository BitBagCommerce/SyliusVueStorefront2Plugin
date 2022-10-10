<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

/** @experimental */
class ChangeItemQuantityInCart implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface
{
    public ?string $orderTokenValue;

    public ?string $orderItemId;

    public int $quantity;

    public function __construct(
        int $quantity,
        ?string $orderItemId = null,
        ?string $orderTokenValue = null,
    ) {
        $this->quantity = $quantity;
        $this->orderItemId = $orderItemId;
        $this->orderTokenValue = $orderTokenValue;
    }

    public static function createFromData(
        string $tokenValue,
        string $orderItemId,
        int $quantity,
    ): self {
        $command = new self($quantity);

        $command->orderTokenValue = $tokenValue;
        $command->orderItemId = $orderItemId;

        return $command;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getSubresourceId(): ?string
    {
        return $this->orderItemId;
    }

    public function setSubresourceId(?string $subresourceId): void
    {
        $this->orderItemId = $subresourceId;
    }

    public function getSubresourceIdAttributeKey(): string
    {
        return 'orderItemId';
    }

    public function getOrderItemId(): ?string
    {
        return $this->orderItemId;
    }

    public function setOrderItemId(?string $orderItemId): void
    {
        $this->orderItemId = $orderItemId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
