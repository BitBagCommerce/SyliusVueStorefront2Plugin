<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProduct;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProductWishlistGeneratorContext extends AbstractGeneratorContext implements ProductWishlistGeneratorContextInterface
{
    private ChannelInterface $channel;

    public function __construct(
        SymfonyStyle $io,
        int $quantity,
        ChannelInterface $channel
    ) {
        parent::__construct($io, $quantity);
        $this->channel = $channel;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function entityName(): string
    {
        return WishlistProduct::class;
    }
}
