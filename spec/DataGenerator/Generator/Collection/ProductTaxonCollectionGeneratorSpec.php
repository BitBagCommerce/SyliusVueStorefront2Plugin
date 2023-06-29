<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductTaxonGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\ProductRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductTaxonFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection\ProductTaxonCollectionGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\SimpleType\Integer\IntegerGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductTaxonCollectionGeneratorSpec extends ObjectBehavior
{
    public function let(
        ProductRepositoryInterface $productRepository,
        ProductTaxonFactoryInterface $productTaxonFactory,
        EntityManagerInterface $entityManager,
        IntegerGeneratorInterface $integerGenerator,
    ): void {
        $this->beConstructedWith($productRepository, $productTaxonFactory, $entityManager, $integerGenerator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductTaxonCollectionGenerator::class);
    }

    public function it_generates_product_taxon_collection(
        ProductRepositoryInterface $productRepository,
        ProductTaxonFactoryInterface $productTaxonFactory,
        IntegerGeneratorInterface $integerGenerator,
        ChannelInterface $channel,
        TaxonInterface $taxon,
        ProductTaxonGeneratorContextInterface $context,
        ProductInterface $product1,
        ProductInterface $product2,
        ProductTaxonInterface $productTaxon1,
        ProductTaxonInterface $productTaxon2,
    ): void {
        $products = [$product1, $product2];
        $productsCount = count($products);
        $min = 0;
        $quantity = 10;
        $stress = 20;
        $topValuesThreshold = 80;
        $randomInt = 2;
        $limit = 100;
        $offset = 0;
        $productTaxons = [$productTaxon1, $productTaxon2];

        $context->getChannel()->willReturn($channel);
        $context->getQuantity()->willReturn($quantity);
        $context->getStress()->willReturn($stress);

        $productRepository->getEntityCount($channel)->willReturn($productsCount);
        $integerGenerator->generateBiased($min, $productsCount, $stress, $topValuesThreshold)->willReturn($randomInt);

        $productRepository->findByChannel($channel, $limit, $offset)->willReturn($products);

        $i = 0;
        foreach ($products as $product) {
            $product->hasTaxon($taxon)->willReturn(false);
            $productTaxonFactory->create($taxon, $product, $i)->willReturn($productTaxons[$i]);
            $product->addProductTaxon($productTaxons[$i])->shouldBeCalled();
            $product->setMainTaxon($taxon)->shouldBeCalled();

            $i++;
        }

        $productRepository->findByChannel($channel, $limit, $offset + $limit)->willReturn([]);

        $this->generate($taxon->getWrappedObject(), $context);
    }

    public function it_does_nothing_if_no_products_found(
        ProductRepositoryInterface $productRepository,
        ChannelInterface $channel,
        TaxonInterface $taxon,
        ProductTaxonGeneratorContextInterface $context,
    ): void {
        $productsCount = 0;
        $quantity = 10;
        $stress = 20;

        $context->getChannel()->willReturn($channel);
        $context->getQuantity()->willReturn($quantity);
        $context->getStress()->willReturn($stress);

        $productRepository->getEntityCount($channel)->willReturn($productsCount);

        $this->generate($taxon->getWrappedObject(), $context);
    }

    public function it_does_nothing_if_product_taxon_exists(
        ProductRepositoryInterface $productRepository,
        IntegerGeneratorInterface $integerGenerator,
        ChannelInterface $channel,
        TaxonInterface $taxon,
        ProductTaxonGeneratorContextInterface $context,
        ProductInterface $product1,
        ProductInterface $product2,
    ): void {
        $products = [$product1, $product2];
        $productsCount = count($products);
        $min = 0;
        $quantity = 10;
        $stress = 20;
        $topValuesThreshold = 80;
        $randomInt = 2;
        $limit = 100;
        $offset = 0;

        $context->getChannel()->willReturn($channel);
        $context->getQuantity()->willReturn($quantity);
        $context->getStress()->willReturn($stress);

        $productRepository->getEntityCount($channel)->willReturn($productsCount);
        $integerGenerator->generateBiased($min, $productsCount, $stress, $topValuesThreshold)->willReturn($randomInt);

        $productRepository->findByChannel($channel, $limit, $offset)->willReturn($products);

        foreach ($products as $product) {
            $product->hasTaxon($taxon)->willReturn(true);
        }

        $productRepository->findByChannel($channel, $limit, $offset + $limit)->willReturn([]);

        $this->generate($taxon->getWrappedObject(), $context);
    }

    public function it_throws_exception_on_invalid_context(
        TaxonInterface $taxon,
        ContextInterface $context,
    ): void {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$taxon, $context]);
    }
}
