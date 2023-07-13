<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Collection;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductTaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Collection\ProductTaxonCollectionBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection\ProductTaxonCollectionGeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProductTaxonCollectionBulkGeneratorSpec extends ObjectBehavior
{
    public function let(
        TaxonRepositoryInterface $taxonRepository,
        ProductTaxonCollectionGeneratorInterface $productTaxonCollectionGenerator,
    ): void {
        $this->beConstructedWith($taxonRepository, $productTaxonCollectionGenerator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductTaxonCollectionBulkGenerator::class);
    }

    public function it_generates_product_taxon_collection(
        TaxonRepositoryInterface $taxonRepository,
        ProductTaxonCollectionGeneratorInterface $productTaxonCollectionGenerator,
        ProductTaxonGeneratorContextInterface $context,
        TaxonInterface $taxon1,
        TaxonInterface $taxon2,
        SymfonyStyle $io,
    ): void {
        $entityCount = 2;
        $limit = 100;
        $offset = 0;
        $taxons = [$taxon1, $taxon2];

        $context->getIO()->willReturn($io);
        $taxonRepository->getEntityCount()->willReturn($entityCount);

        $taxonRepository->findBatch($limit, $offset)->willReturn($taxons);

        foreach ($taxons as $taxon) {
            $productTaxonCollectionGenerator->generate($taxon, $context)->shouldBeCalled();
        }

        $taxonRepository->findBatch($limit, $offset + $limit)->willReturn([]);

        $this->generate($context);
    }

    public function it_does_nothing_if_no_taxons_found(
        TaxonRepositoryInterface $taxonRepository,
        ProductTaxonGeneratorContextInterface $context,
        SymfonyStyle $io,
    ): void {
        $entityCount = 0;
        $limit = 100;
        $offset = 0;

        $context->getIO()->willReturn($io);
        $taxonRepository->getEntityCount()->willReturn($entityCount);

        $taxonRepository->findBatch($limit, $offset)->willReturn([]);

        $this->generate($context);
    }

    public function it_throws_exception_on_invalid_context(ContextInterface $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
