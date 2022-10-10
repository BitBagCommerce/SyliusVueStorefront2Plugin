<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

/** @experimental */
class ShippingAddressOrder implements OrderTokenValueAwareInterface
{
    public ?string $orderTokenValue;

    /** @psalm-immutable */
    public ?string $email;

    /** @psalm-immutable */
    public ?AddressInterface $shippingAddress;

    public function __construct(
        ?string $email,
        ?string $orderTokenValue,
        ?AddressInterface $shippingAddress = null,
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
