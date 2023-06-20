<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\TaxonContext;
use PhpSpec\ObjectBehavior;

final class TaxonContextSpec extends ObjectBehavior
{
    private const MAX_TAXON_LEVEL = 10;

    private const MAX_CHILDREN_PER_TAXON_LEVEL = 5;

    public function let(): void
    {
        $this->beConstructedWith(
            self::MAX_TAXON_LEVEL,
            self::MAX_CHILDREN_PER_TAXON_LEVEL,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TaxonContext::class);
    }

    public function it_returns_max_taxon_level(): void
    {
        $this->getMaxTaxonLevel()->shouldReturn(self::MAX_TAXON_LEVEL);
    }

    public function it_returns_max_children_per_taxon_level(): void
    {
        $this->getMaxChildrenPerTaxonLevel()->shouldReturn(self::MAX_CHILDREN_PER_TAXON_LEVEL);
    }
}
