<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProductGeneratorContext extends AbstractGeneratorContext implements ProductGeneratorContextInterface
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
        return Product::class;
    }
}
