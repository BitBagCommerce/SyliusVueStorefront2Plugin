<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\GeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context\GeneratorContextFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\CompositeBulkGenerator;
use PhpSpec\ObjectBehavior;

final class CompositeBulkGeneratorSpec extends ObjectBehavior
{
    public function let(
        BulkGeneratorInterface $generator1,
        BulkGeneratorInterface $generator2,
        GeneratorContextFactoryInterface $bulkGeneratorContextFactory,
    ): void {
        $this->beConstructedWith([$generator1, $generator2], $bulkGeneratorContextFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CompositeBulkGenerator::class);
    }

    public function it_generates_entities(
        GeneratorContextFactoryInterface $bulkGeneratorContextFactory,
        DataGeneratorCommandContextInterface $context,
        BulkGeneratorInterface $generator1,
        BulkGeneratorInterface $generator2,
        GeneratorContextInterface $bulkContext1,
        GeneratorContextInterface $bulkContext2,
    ): void {
        $bulkGeneratorContextFactory
            ->fromCommandContext(
                $context->getWrappedObject(),
                $generator1->getWrappedObject(),
            )
            ->willReturn($bulkContext1->getWrappedObject());

        $generator1->generate($bulkContext1->getWrappedObject())->shouldBeCalled();

        $bulkGeneratorContextFactory
            ->fromCommandContext(
                $context->getWrappedObject(),
                $generator2->getWrappedObject(),
            )
            ->willReturn($bulkContext2->getWrappedObject());

        $generator2->generate($bulkContext2->getWrappedObject())->shouldBeCalled();

        $this->generate($context);
    }

    public function it_throws_exception_on_invalid_context(GeneratorContextInterface $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
