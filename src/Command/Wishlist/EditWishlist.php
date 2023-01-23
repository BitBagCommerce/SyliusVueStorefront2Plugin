<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Wishlist;

final class EditWishlist
{
    public string $id;

    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
