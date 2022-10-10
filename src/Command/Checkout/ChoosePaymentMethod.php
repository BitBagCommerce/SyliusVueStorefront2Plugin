<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Command\PaymentMethodCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

/** @experimental */
class ChoosePaymentMethod implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface, PaymentMethodCodeAwareInterface
{
    public ?string $orderTokenValue;

    public ?string $paymentId;

    public ?string $paymentMethodCode;

    public function __construct(
        string $orderTokenValue,
        string $paymentMethodCode,
        ?string $paymentId,
    ) {
        $this->orderTokenValue = $orderTokenValue;
        $this->paymentMethodCode = $paymentMethodCode;
        $this->paymentId = $paymentId;
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
        return $this->paymentId;
    }

    public function setSubresourceId(?string $subresourceId): void
    {
        $this->paymentId = $subresourceId;
    }

    public function getSubresourceIdAttributeKey(): string
    {
        return 'paymentId';
    }

    public function getPaymentMethodCode(): ?string
    {
        return $this->paymentMethodCode;
    }

    public function setPaymentMethodCode(?string $paymentMethodCode): void
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(?string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }
}
