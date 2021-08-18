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
