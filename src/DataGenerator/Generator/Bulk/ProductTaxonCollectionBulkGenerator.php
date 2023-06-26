<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductTaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection\ProductTaxonCollectionGeneratorInterface;
use DateTime;

final class ProductTaxonCollectionBulkGenerator implements ProductTaxonCollectionBulkGeneratorInterface
{
    private TaxonRepositoryInterface $taxonRepository;

    private ProductTaxonCollectionGeneratorInterface $productTaxonCollectionGenerator;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ProductTaxonCollectionGeneratorInterface $productTaxonCollectionGenerator,
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->productTaxonCollectionGenerator = $productTaxonCollectionGenerator;
    }

    public function generate(ContextInterface $context): void
    {
        if (!$context instanceof ProductTaxonGeneratorContextInterface) {
            throw new InvalidContextException();
        }

        $io = $context->getIO();

        $io->info(sprintf(
            '%s Generating ProductTaxons',
            (new DateTime())->format('Y-m-d H:i:s'),
        ));

        $offset = 0;

        $io->progressStart($this->taxonRepository->getEntityCount());

        while (
            count($taxons = $this->taxonRepository->findBatch(self::LIMIT, $offset)) > 0
        ) {
            foreach ($taxons as $taxon) {
                $this->productTaxonCollectionGenerator->generate($taxon, $context);
                $io->progressAdvance();
            }

            $offset += self::LIMIT;
        }

        $io->progressFinish();
    }
}
