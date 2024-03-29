<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\TaxonFactory;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $taxonFactory): void
    {
        $this->beConstructedWith($taxonFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TaxonFactory::class);
    }

    public function it_creates_taxon(
        FactoryInterface $taxonFactory,
        TaxonInterface $parent,
        TaxonInterface $sibling,
        TaxonTranslationInterface $translation,
        TaxonInterface $taxon,
    ): void {
        $taxonFactory->createNew()->willReturn($taxon);
        $parent->getChildren()->willReturn(new ArrayCollection([$sibling]));

        $this
            ->create(
                Argument::type('string'),
                $parent,
                $translation
            )
            ->shouldReturn($taxon);
    }
}
