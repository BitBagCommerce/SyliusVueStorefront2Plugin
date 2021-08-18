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
use Sylius\Bundle\ApiBundle\Command\PaymentMethodCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

/** @experimental */
class ChoosePaymentMethod implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface, PaymentMethodCodeAwareInterface
{
    public ?string $orderTokenValue;

    /** @psalm-immutable */
    public ?string $paymentId;

    /** @psalm-immutable */
    public ?string $paymentMethodCode;

    public function __construct(string $orderTokenValue, string $paymentMethodCode)
    {
        $this->orderTokenValue = $orderTokenValue;
        $this->paymentMethodCode = $paymentMethodCode;
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
}
