<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\TaxonTranslationFactory;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;

final class TaxonTranslationFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TaxonTranslationFactory::class);
    }

    public function it_creates(): void
    {
        $name = 'Test taxon translation';
        $slug = 'test-taxon-translation';
        $locale = 'en-US';

        $translation = new TaxonTranslation();

        $translation->setName($name);
        $translation->setSlug($slug);
        $translation->setLocale($locale);

        $this->create($name, $locale);
    }
}
