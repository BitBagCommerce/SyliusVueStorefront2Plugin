<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

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
        $rand = rand(0, PHP_INT_MAX);
        return $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('child'.$rand)
            ->innerJoin('o.parent', 'parent'.$rand)
            ->leftJoin('o.children', 'child'.$rand)
            ->andWhere('o.enabled = true')
            ->andWhere('parent'.$rand.'.code = :parentCode')
            ->addOrderBy('o.position')
            ->setParameter('parentCode', ($menuTaxon !== null) ? $menuTaxon->getCode() : 'category')
        ;
    }
}
