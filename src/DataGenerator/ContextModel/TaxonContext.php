<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel;

class TaxonContext implements TaxonContextInterface
{
    private int $maxTaxonLevel;

    private int $maxChildrenPerTaxonLevel;

    public function __construct(
        int $maxTaxonLevel,
        int $maxChildrenPerTaxonLevel,
    ) {
        $this->maxTaxonLevel = $maxTaxonLevel;
        $this->maxChildrenPerTaxonLevel = $maxChildrenPerTaxonLevel;
    }

    public function getMaxTaxonLevel(): int
    {
        return $this->maxTaxonLevel;
    }

    public function getMaxChildrenPerTaxonLevel(): int
    {
        return $this->maxChildrenPerTaxonLevel;
    }

    public static function entityName(): string
    {
        return 'Taxon';
    }
}
