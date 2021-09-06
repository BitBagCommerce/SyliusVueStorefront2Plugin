<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

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

    public static function removeFromData(string $tokenValue, string $orderItemId): self
    {
        return new self($tokenValue, $orderItemId);
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
