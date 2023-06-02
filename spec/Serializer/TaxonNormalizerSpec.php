<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Serializer;

use BitBag\SyliusVueStorefront2Plugin\Serializer\TaxonNormalizer;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonNormalizerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TaxonNormalizer::class);
    }

    public function it_normalizes_taxon(
        TaxonInterface $taxon,
        TaxonInterface $taxonParent,
        TaxonTranslationInterface $translation,
    ): void {
        $id = 111;
        $parentId = 100;
        $name = 'Test taxon name';
        $code = 'test_taxon_code';
        $position = 1;
        $slug = 'test_taxon_slug';
        $description = 'Test taxon description';
        $enabled = true;
        $level = 1;

        $context = [
            ContextKeys::LOCALE_CODE => 'en_US',
        ];

        $locale = $context[ContextKeys::LOCALE_CODE];
        $taxon->getTranslation($locale)->willReturn($translation);

        $taxon->getId()->willReturn($id);
        $translation->getName()->willReturn($name);
        $taxon->getName()->willReturn($name);
        $taxon->getCode()->willReturn($code);
        $taxon->getPosition()->willReturn($position);
        $translation->getSlug()->willReturn($slug);
        $taxon->getSlug()->willReturn($slug);
        $translation->getDescription()->willReturn($description);
        $taxon->getDescription()->willReturn($description);
        $taxon->getParent()->willReturn($taxonParent);
        $taxon->isEnabled()->willReturn($enabled);
        $taxon->getLevel()->willReturn($level);
        $taxon->getTranslation()->willReturn($translation);
        $taxonParent->getId()->willReturn($parentId);

        $result = [
            'id' => $id,
            'name' => $name,
            'code' => $code,
            'position' => $position,
            'slug' => $slug,
            'description' => $description,
            'parent' => [
                'id' => $parentId,
            ],
            'enabled' => $enabled,
            'level' => $level,
        ];

        $this->normalize($taxon, null, $context)->shouldReturn($result);
    }

    public function it_checks_if_it_supports_normalization(Taxon $taxon): void
    {
        $this->supportsNormalization($taxon)->shouldReturn(true);
    }
}
