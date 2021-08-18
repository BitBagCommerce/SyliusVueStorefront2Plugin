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
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

/** @experimental */
class ChangeItemQuantityInCart implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface
{
    public ?string $orderTokenValue;

    public ?string $orderItemId;

    /** @psalm-immutable */
    public int $quantity;

    public function __construct(int $quantity, ?string $orderItemId = null)
    {
        $this->quantity = $quantity;
        $this->orderItemId = $orderItemId;
    }

    public static function createFromData(string $tokenValue, string $orderItemId, int $quantity): self
    {
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
}
