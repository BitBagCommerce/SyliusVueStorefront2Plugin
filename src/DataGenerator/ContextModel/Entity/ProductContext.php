<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;

final class ProductContext implements ProductContextInterface
{
    private ChannelInterface $channel;

    public function __construct(ChannelInterface $channel)
    {
        $this->channel = $channel;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function className(): string
    {
        return Product::class;
    }
}