<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\Command\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\BillingAddressOrder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\AddressInterface;

final class BillingAddressOrderSpec extends ObjectBehavior
{
    function let(AddressInterface $billingAddress): void
    {
        $this->beConstructedWith('johndoe@mail.com', 'orderTokenValue', $billingAddress);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(BillingAddressOrder::class);
    }

    function it_implements_order_token_value_interface(): void
    {
        $this->shouldImplement(OrderTokenValueAwareInterface::class);
    }
}
