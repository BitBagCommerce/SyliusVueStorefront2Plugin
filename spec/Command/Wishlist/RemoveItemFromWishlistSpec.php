<?php

namespace spec\BitBag\SyliusVueStorefront2Plugin\Command\Wishlist;

use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\RemoveItemFromWishlist;
use PhpSpec\ObjectBehavior;

class RemoveItemFromWishlistSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('wishlistIri', 'productVariantIri');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveItemFromWishlist::class);
    }
}
