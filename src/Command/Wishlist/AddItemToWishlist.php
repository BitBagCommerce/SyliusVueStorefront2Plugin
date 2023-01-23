<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Wishlist;

final class AddItemToWishlist
{
    public string $id;

    public string $productVariant;

    public function __construct(string $id, string $productVariant)
    {
        $this->id = $id;
        $this->productVariant = $productVariant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProductVariant(): string
    {
        return $this->productVariant;
    }
}
