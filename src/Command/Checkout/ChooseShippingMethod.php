<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
