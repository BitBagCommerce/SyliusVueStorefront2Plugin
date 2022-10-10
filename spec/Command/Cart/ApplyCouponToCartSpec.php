<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Command\Cart;

use BitBag\SyliusVueStorefront2Plugin\Command\Cart\ApplyCouponToCart;
use PhpSpec\ObjectBehavior;

final class ApplyCouponToCartSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('couponCode', 'orderTokenValue');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ApplyCouponToCart::class);
    }

    function it_gets_order_token_value(): void
    {
        $this->getOrderTokenValue()->shouldReturn('orderTokenValue');
    }
}
