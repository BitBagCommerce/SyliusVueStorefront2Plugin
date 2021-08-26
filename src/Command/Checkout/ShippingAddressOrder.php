<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

/** @experimental */
final class ShippingAddressOrder implements OrderTokenValueAwareInterface
{
    public ?string $orderTokenValue;

    /** @psalm-immutable */
    public ?string $email;

    /** @psalm-immutable */
    public ?AddressInterface $shippingAddress;

    public function __construct(
        ?string $email,
        ?string $orderTokenValue,
        ?AddressInterface $shippingAddress = null
    ) {
        $this->email = $email;
        $this->orderTokenValue = $orderTokenValue;
        $this->shippingAddress = $shippingAddress;
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
