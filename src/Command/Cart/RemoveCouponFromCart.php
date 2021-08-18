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
class RemoveCouponFromCart implements OrderTokenValueAwareInterface
{
    public ?string $orderTokenValue;

    public string $couponCode;

    public function __construct(?string $orderTokenValue, string $couponCode)
    {
        $this->orderTokenValue = $orderTokenValue;
        $this->couponCode = $couponCode;
    }

    public static function removeFromData(string $tokenValue, string $couponCode): self
    {
        return new self($tokenValue, $couponCode);
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
