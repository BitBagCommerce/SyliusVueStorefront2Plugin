<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Resolver\Mutation\Wishlist;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use BitBag\SyliusVueStorefront2Plugin\Resolver\Mutation\Wishlist\ClearWishlistResolver;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ClearWishlistResolverSpec extends ObjectBehavior
{
    public function let(EventDispatcherInterface $eventDispatcher): void
    {
        $this->beConstructedWith($eventDispatcher);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ClearWishlistResolver::class);
        $this->shouldHaveType(MutationResolverInterface::class);
    }

    public function it_is_invokable(
        WishlistInterface $wishlist,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $context = [
            'args' => [
                'input' => [],
            ],
        ];

        $wishlist->clear()->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), ClearWishlistResolver::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($wishlist, $context)->shouldReturn($wishlist);
    }
}
