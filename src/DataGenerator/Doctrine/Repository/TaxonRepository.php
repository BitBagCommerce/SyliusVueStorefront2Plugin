<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository;

use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;
use Sylius\Component\Core\Model\TaxonInterface;

final class TaxonRepository extends BaseTaxonRepository implements TaxonRepositoryInterface
{
    public function getMainTaxon(): TaxonInterface
    {
        return $this->createQueryBuilder('taxon')
            ->where('taxon.parent IS NULL')
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * @return array<TaxonInterface>
     */
    public function findEligibleParents(
        int $maxTaxonLevel,
        int $maxChildrenPerTaxonLevel,
        int $limit = self::DEFAULT_LIMIT,
    ): array {
        return $this->createQueryBuilder('taxon')
            ->leftJoin('taxon.children', 'child')
            ->select('taxon')
            ->where('taxon.level < :maxTaxonLevel')
            ->having('COUNT(child) < :maxChildrenPerTaxonLevel')
            ->groupBy('taxon')
            ->orderBy('taxon.level', 'desc')
            ->addOrderBy('taxon.left', 'asc')
            ->setMaxResults($limit)
            ->setParameter('maxTaxonLevel', $maxTaxonLevel)
            ->setParameter('maxChildrenPerTaxonLevel', $maxChildrenPerTaxonLevel)
            ->getQuery()
            ->getResult()
        ;
    }
}
