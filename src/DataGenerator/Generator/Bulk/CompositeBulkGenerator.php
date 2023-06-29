<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context\GeneratorContextFactoryInterface;

final class CompositeBulkGenerator implements BulkGeneratorInterface
{
    /** @var iterable|BulkGeneratorInterface[] */
    private iterable $generators;

    private GeneratorContextFactoryInterface $bulkGeneratorContextFactory;

    /** @param iterable|BulkGeneratorInterface[] $generators */
    public function __construct(
        iterable $generators,
        GeneratorContextFactoryInterface $bulkGeneratorContextFactory,
    ) {
        $this->generators = $generators;
        $this->bulkGeneratorContextFactory = $bulkGeneratorContextFactory;
    }

    public function generate(ContextInterface $context): void
    {
        if (!$context instanceof DataGeneratorCommandContextInterface) {
            throw new InvalidContextException();
        }

        foreach ($this->generators as $generator) {
            $bulkContext = $this->bulkGeneratorContextFactory->fromCommandContext($context, $generator);

            $generator->generate($bulkContext);
        }
    }
}
