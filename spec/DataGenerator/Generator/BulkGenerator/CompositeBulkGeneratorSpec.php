<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext\BulkContext;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\BulkContext\BulkContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context\BulkGeneratorContextFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\BulkGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\BulkGenerator\CompositeBulkGenerator;
use PhpSpec\ObjectBehavior;

class CompositeBulkGeneratorSpec extends ObjectBehavior
{
    public function let(
        BulkGeneratorInterface $generator1,
        BulkGeneratorInterface $generator2,
        BulkGeneratorContextFactoryInterface $bulkGeneratorContextFactory,
    ): void {
        $this->beConstructedWith([$generator1, $generator2], $bulkGeneratorContextFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CompositeBulkGenerator::class);
    }

    public function it_generates(
        BulkGeneratorContextFactoryInterface $bulkGeneratorContextFactory,
        DataGeneratorCommandContextInterface $context,
        BulkGeneratorInterface $generator1,
        BulkGeneratorInterface $generator2,
        BulkContextInterface $bulkContext1,
        BulkContextInterface $bulkContext2,
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

    public function it_throws_exception_on_invalid_context(BulkContext $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
