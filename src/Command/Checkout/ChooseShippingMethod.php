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
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

/** @experimental */
class ChooseShippingMethod implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface
{
    public ?string $orderTokenValue;

    public ?string $shipmentId;

    /** @psalm-immutable */
    public string $shippingMethodCode;

    public function __construct(string $orderTokenValue, string $shippingMethodCode, string $shipmentId)
    {
        $this->orderTokenValue = $orderTokenValue;
        $this->shippingMethodCode = $shippingMethodCode;
        $this->shipmentId = $shipmentId;
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
        return $this->shipmentId;
    }

    public function setSubresourceId(?string $subresourceId): void
    {
        $this->shipmentId = $subresourceId;
    }

    public function getSubresourceIdAttributeKey(): string
    {
        return 'shipmentId';
    }
}
