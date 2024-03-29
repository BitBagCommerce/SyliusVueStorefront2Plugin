<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Resolver\Mutation\Wishlist;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

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
        Assert::isInstanceOf($item, WishlistInterface::class);
        $item->clear();

        /** @var array $input */
        $input = $context['args']['input'];

        $this->eventDispatcher->dispatch(new GenericEvent($item, $input), self::EVENT_NAME);

        return $item;
    }
}
