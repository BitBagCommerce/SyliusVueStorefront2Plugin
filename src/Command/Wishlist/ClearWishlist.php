<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Wishlist;

final class ClearWishlist
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
