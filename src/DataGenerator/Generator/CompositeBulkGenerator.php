<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContextInterface;

final class CompositeBulkGenerator implements BulkGeneratorInterface
{
    /** @var BulkGeneratorInterface[] */
    private array $generators;

    /** @param BulkGeneratorInterface[] $generators */
    public function __construct(array $generators) {
        $this->generators = $generators;
    }

    public function generate(): void
    {
        foreach ($this->generators as $generator) {
            $generator->generate();
        }
    }

    public function setContext(BulkContextInterface $context): void
    {
    }
}
