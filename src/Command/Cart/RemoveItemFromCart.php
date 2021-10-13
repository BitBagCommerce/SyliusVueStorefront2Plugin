<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

/** @experimental */
class RemoveItemFromCart implements OrderTokenValueAwareInterface
{
    public ?string $orderTokenValue;

    /** @psalm-immutable */
    public string $itemId;

    public function __construct(?string $orderTokenValue, string $orderItemId)
    {
        $this->orderTokenValue = $orderTokenValue;
        $this->itemId = $orderItemId;
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
