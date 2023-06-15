<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\EntityContextInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

interface BulkContextInterface extends ContextInterface
{
    public function getQuantity(): int;

    public function getIO(): SymfonyStyle;

    public function getEntityContext(): EntityContextInterface;
}
