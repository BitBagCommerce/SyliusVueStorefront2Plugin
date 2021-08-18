<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
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
