<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use Sylius\Component\Resource\Model\ResourceInterface;

interface FactoryInterface
{
    const FLUSH_AFTER = 100;

    public function setQuantity(int $quantity): void;

    public function create(): ResourceInterface;

    public function entityName(): string;
}
