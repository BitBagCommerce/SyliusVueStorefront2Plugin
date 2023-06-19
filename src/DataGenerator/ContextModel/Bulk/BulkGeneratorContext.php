<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Bulk;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\EntityContextInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class BulkGeneratorContext implements BulkGeneratorContextInterface
{
    private int $quantity;

    private SymfonyStyle $io;

    private EntityContextInterface $context;

    public function __construct(
        int $quantity,
        SymfonyStyle $io,
        EntityContextInterface $context,
    ) {
        $this->quantity = $quantity;
        $this->io = $io;
        $this->context = $context;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getIO(): SymfonyStyle
    {
        return $this->io;
    }

    public function getEntityContext(): EntityContextInterface
    {
        return $this->context;
    }
}
