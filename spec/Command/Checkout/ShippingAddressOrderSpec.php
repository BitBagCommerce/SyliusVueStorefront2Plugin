<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\Command\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\ShippingAddressOrder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\AddressInterface;

class ShippingAddressOrderSpec extends ObjectBehavior
{
    function let(AddressInterface $shippingAddress): void
    {
        $this->beConstructedWith('bruce@wayne.co', 'orderTokenValue', $shippingAddress);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingAddressOrder::class);
    }

    function it_implements_order_token_value_interface(): void
    {
        $this->shouldImplement(OrderTokenValueAwareInterface::class);
    }
}
