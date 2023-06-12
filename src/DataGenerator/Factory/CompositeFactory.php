<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

final class CompositeFactory implements BulkableInterface
{
    /**
     * @var BulkableInterface[]
     */
    private array $factories;

    /**
     * @param BulkableInterface[] $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    public function bulkCreate(): void
    {
        foreach ($this->factories as $factory) {
            $factory->bulkCreate();
        }
    }
}
