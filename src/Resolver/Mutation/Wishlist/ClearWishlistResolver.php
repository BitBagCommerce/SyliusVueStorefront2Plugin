<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Resolver\Mutation\Wishlist;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ClearWishlistResolver implements MutationResolverInterface
{
    public const EVENT_NAME = 'bitbag.sylius_vue_storefront2.mutation_resolver.clear_wishlist.complete';

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke($item, array $context): WishlistInterface
    {
        /** @var WishlistInterface $item */
        $item->clear();

        /** @var array $input */
        $input = $context['args']['input'];

        $this->eventDispatcher->dispatch(new GenericEvent($item, $input), self::EVENT_NAME);

        return $item;
    }
}
