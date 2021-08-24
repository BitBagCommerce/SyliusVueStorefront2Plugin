<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Doctrine\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonRepository extends BaseTaxonRepository implements TaxonRepositoryInterface
{
    public function createChildrenByChannelMenuTaxonQueryBuilder(
        ?TaxonInterface $menuTaxon = null,
        ?string $locale = null
    ): QueryBuilder {
        return $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('child')
            ->innerJoin('o.parent', 'parent')
            ->leftJoin('o.children', 'child')
            ->andWhere('o.enabled = true')
            ->andWhere('parent.code = :parentCode')
            ->addOrderBy('o.position')
            ->setParameter('parentCode', ($menuTaxon !== null) ? $menuTaxon->getCode() : 'category');
    }
}
