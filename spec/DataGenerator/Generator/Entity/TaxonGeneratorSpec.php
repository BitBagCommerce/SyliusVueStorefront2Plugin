<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\GeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\TaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\TaxonFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity\TaxonGenerator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonGeneratorSpec extends ObjectBehavior
{
    public function let(
        TaxonFactoryInterface $taxonFactory,
        TaxonRepositoryInterface $taxonRepository,
    ): void {
        $this->beConstructedWith($taxonFactory, $taxonRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TaxonGenerator::class);
    }

    public function it_generates_taxon_with_main_taxon_as_parent(
        TaxonFactoryInterface $taxonFactory,
        TaxonRepositoryInterface $taxonRepository,
        TaxonGeneratorContextInterface $context,
        TaxonInterface $parent,
        TaxonInterface $taxon,
    ): void {
        $maxTaxonLevel = 100;
        $maxChildrenPerTaxonLevel = 100;

        $context->getMaxTaxonLevel()->willReturn($maxTaxonLevel);
        $context->getMaxChildrenPerTaxonLevel()->willReturn($maxChildrenPerTaxonLevel);
        $taxonRepository->findEligibleParents($maxTaxonLevel, $maxChildrenPerTaxonLevel)
            ->willReturn([]);
        $taxonRepository->getMainTaxon()->willReturn($parent->getWrappedObject());

        $taxonFactory
            ->create(
                Argument::type('string'),
                $parent->getWrappedObject(),
                Argument::type(TaxonTranslationInterface::class)
            )
            ->willReturn($taxon->getWrappedObject());

        $this->generate($context)->shouldReturn($taxon);
    }

    public function it_generates_taxon_with_random_parent(
        TaxonFactoryInterface $taxonFactory,
        TaxonRepositoryInterface $taxonRepository,
        TaxonGeneratorContextInterface $context,
        TaxonInterface $parent,
        TaxonInterface $taxon,
    ): void {
        $maxTaxonLevel = 100;
        $maxChildrenPerTaxonLevel = 100;

        $context->getMaxTaxonLevel()->willReturn($maxTaxonLevel);
        $context->getMaxChildrenPerTaxonLevel()->willReturn($maxChildrenPerTaxonLevel);
        $taxonRepository->findEligibleParents($maxTaxonLevel, $maxChildrenPerTaxonLevel)
            ->willReturn([$parent->getWrappedObject()]);

        $taxonFactory
            ->create(
                Argument::type('string'),
                $parent->getWrappedObject(),
                Argument::type(TaxonTranslationInterface::class)
            )
            ->willReturn($taxon->getWrappedObject());

        $this->generate($context)->shouldReturn($taxon);
    }

    public function it_throws_exception_on_invalid_context(GeneratorContextInterface $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
