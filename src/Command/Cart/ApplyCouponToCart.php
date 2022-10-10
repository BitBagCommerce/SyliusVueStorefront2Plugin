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
class ApplyCouponToCart implements OrderTokenValueAwareInterface
{
    public ?string $orderTokenValue;

    public ?string $couponCode;

    public function __construct(?string $couponCode, ?string $orderTokenValue)
    {
        $this->couponCode = $couponCode;
        $this->orderTokenValue = $orderTokenValue;
    }

    public static function createFromData(string $orderTokenValue, ?string $couponCode): self
    {
        $command = new self($couponCode, $orderTokenValue);

        $command->setOrderTokenValue($orderTokenValue);

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
}
