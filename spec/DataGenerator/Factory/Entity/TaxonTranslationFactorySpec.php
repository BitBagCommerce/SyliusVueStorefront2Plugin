<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\TaxonTranslationFactory;
use Gedmo\Sluggable\Util\Urlizer;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonTranslationFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TaxonTranslationFactory::class);
    }

    public function it_creates_taxon_translation(): void
    {
        $name = 'Example Taxon';
        $locale = 'en_US';

        $translation = $this->create($name, $locale);

        $translation->shouldBeAnInstanceOf(TaxonTranslationInterface::class);
        $translation->getName()->shouldBe($name);
        $translation->getLocale()->shouldBe($locale);
        $translation->getSlug()->shouldBe(Urlizer::transliterate($name));
    }
}
