<?php

namespace spec\BitBag\SyliusVueStorefront2Plugin\Command\Wishlist;

use BitBag\SyliusVueStorefront2Plugin\Command\Wishlist\CreateNewWishlist;
use PhpSpec\ObjectBehavior;

class CreateNewWishlistSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('Wishlist', 'channelCode');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateNewWishlist::class);
    }
}
