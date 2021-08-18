<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Doctrine\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonRepositoryInterface extends RepositoryInterface
{
    public function createChildrenByChannelMenuTaxonQueryBuilder(
        ?TaxonInterface $menuTaxon = null,
        ?string $locale = null
    ): QueryBuilder;
}
