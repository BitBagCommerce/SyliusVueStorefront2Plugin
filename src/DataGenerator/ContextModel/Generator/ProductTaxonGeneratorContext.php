<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductTaxon;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProductTaxonGeneratorContext extends AbstractGeneratorContext implements ProductTaxonGeneratorContextInterface
{
    private ChannelInterface $channel;

    private int $stress;

    public function __construct(
        SymfonyStyle $io,
        int $quantity,
        ChannelInterface $channel,
        int $stress,
    ) {
        parent::__construct($io, $quantity);
        $this->channel = $channel;
        $this->stress = $stress;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getStress(): int
    {
        return $this->stress;
    }

    public function entityName(): string
    {
        return ProductTaxon::class;
    }
}
