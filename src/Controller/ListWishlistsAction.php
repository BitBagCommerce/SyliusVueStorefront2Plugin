<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Controller;

use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListWishlistsAction
{
    public WishlistsResolverInterface $wishlistsResolver;

    /**
     * @param WishlistsResolverInterface $wishlistsResolver
     */
    public function __construct(WishlistsResolverInterface $wishlistsResolver)
    {
        $this->wishlistsResolver = $wishlistsResolver;
    }

    public function __invoke(Request $request): Response
    {
        return new JsonResponse($this->wishlistsResolver->resolve());
    }
}
