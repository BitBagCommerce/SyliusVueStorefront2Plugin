<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator;

use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractGeneratorContext implements GeneratorContextInterface
{
    private SymfonyStyle $io;

    private int $quantity;

    public function __construct(
        SymfonyStyle $io,
        int $quantity,
    ) {
        $this->io = $io;
        $this->quantity = $quantity;
    }

    public function getIO(): SymfonyStyle
    {
        return $this->io;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    abstract function entityName(): string;
}
