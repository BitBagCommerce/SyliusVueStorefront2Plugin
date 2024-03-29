<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface as BaseTaxonRepositoryInterface;

interface TaxonRepositoryInterface extends BaseTaxonRepositoryInterface
{
    public function createChildrenByParentQueryBuilder(
        ?TaxonInterface $menuTaxon = null,
        ?string $locale = null,
    ): QueryBuilder;
}
