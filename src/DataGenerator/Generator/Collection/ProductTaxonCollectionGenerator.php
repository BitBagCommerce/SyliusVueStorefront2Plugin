<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductTaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\ProductRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductTaxonFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\SimpleType\Integer\IntegerGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductTaxonCollectionGenerator implements ProductTaxonCollectionGeneratorInterface
{
    private ProductRepositoryInterface $productRepository;

    private ProductTaxonFactoryInterface $productTaxonFactory;

    private EntityManagerInterface $entityManager;

    private IntegerGeneratorInterface $integerGenerator;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductTaxonFactoryInterface $productTaxonFactory,
        EntityManagerInterface $entityManager,
        IntegerGeneratorInterface $integerGenerator,
    ) {
        $this->productRepository = $productRepository;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->entityManager = $entityManager;
        $this->integerGenerator = $integerGenerator;
    }

    public function generate(
        TaxonInterface $taxon,
        ContextInterface $context,
    ): void {
        if (!$context instanceof ProductTaxonGeneratorContextInterface) {
            throw new InvalidContextException();
        }

        $channel = $context->getChannel();
        $productsCount = $this->productRepository->getEntityCount($channel);
        if ($productsCount === 0) {
            return;
        }

        $randomInt = $this->integerGenerator->generateBiased(
            0,
            min($productsCount, $context->getQuantity()),
            $context->getStress(),
            self::TOP_VALUES_THRESHOLD,
        );

        $maxOffset = max(0, $productsCount - $randomInt);
        $offset = mt_rand(0, $maxOffset);
        $i = 0;

        while (
            count($products = $this->productRepository->findByChannel($channel, self::LIMIT, $offset)) > 0
        ) {
            foreach ($products as $product) {
                if ($product->hasTaxon($taxon)) {
                    continue;
                }
                $productTaxon = $this->productTaxonFactory->create($taxon, $product, $i);
                $product->addProductTaxon($productTaxon);
                $product->setMainTaxon($taxon);

                $this->entityManager->persist($product);

                $i++;
                if ($i % self::FLUSH_AFTER === 0) {
                    $this->entityManager->flush();
                }
            }

            $offset += self::LIMIT;

            $this->entityManager->flush();
        }
    }
}
