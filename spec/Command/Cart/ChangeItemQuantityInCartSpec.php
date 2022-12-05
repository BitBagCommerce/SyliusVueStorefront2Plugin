<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Command\Cart;

use BitBag\SyliusVueStorefront2Plugin\Command\Cart\ChangeItemQuantityInCart;
use PhpSpec\ObjectBehavior;

final class ChangeItemQuantityInCartSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(1, 'orderItemId', 'orderTokenValue');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ChangeItemQuantityInCart::class);
    }

    public function it_gets_subresource_id_attribute_key(): void
    {
        $this->getSubresourceIdAttributeKey()->shouldReturn('orderItemId');
    }
}
