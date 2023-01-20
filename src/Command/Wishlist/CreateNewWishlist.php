<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Command\Wishlist;

final class CreateNewWishlist
{
    public string $name;

    public string $channelCode;

    public function __construct(string $name, string $channelCode)
    {
        $this->name = $name;
        $this->channelCode = $channelCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getChannelCode(): string
    {
        return $this->channelCode;
    }
}
