<?php

namespace spec\BitBag\SyliusVueStorefront2Plugin\Command\Wishlist;

use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\AddItemToWishlist;
use PhpSpec\ObjectBehavior;

class AddItemToWishlistSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('wishlistIri', 'productVariantIri');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddItemToWishlist::class);
    }
}
