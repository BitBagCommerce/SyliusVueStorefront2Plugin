<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductTaxonFactory;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductTaxonFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductTaxonFactory::class);
    }

    public function it_creates_product_taxon(
        TaxonInterface $taxon,
        ProductInterface $product,
    ): void {
        $position = 7;
        $productTaxon = $this->create($taxon,$product, $position);

        $productTaxon->shouldBeAnInstanceOf(ProductTaxonInterface::class);
        $productTaxon->getTaxon()->shouldBe($taxon);
        $productTaxon->getProduct()->shouldBe($product);
        $productTaxon->getPosition()->shouldBe($position);
    }
}
