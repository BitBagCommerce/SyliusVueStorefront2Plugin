<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\NoTaxonFoundException;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository as BaseTaxonRepository;
use Sylius\Component\Core\Model\TaxonInterface;

final class TaxonRepository extends BaseTaxonRepository implements TaxonRepositoryInterface
{
    public function getMainTaxon(): TaxonInterface
    {
        return $this->createQueryBuilder('taxon')
            ->where('taxon.parent IS NULL')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @return TaxonInterface[]
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
            ->getResult();
    }

    /**
     * @return TaxonInterface[]
     */
    public function findBatch(
        int $limit = null,
        int $offset = null,
    ): array {
        return $this->createQueryBuilder('taxon')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function getEntityCount(): int
    {
        $queryBuilder = $this->createQueryBuilder('taxon')
            ->andWhere('taxon.enabled = true')
            ->select('COUNT(taxon)');

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getRandomTaxon(): TaxonInterface
    {
        $randomOffset = max(0, rand(0, $this->getEntityCount() - 1));

        $result = $this->createQueryBuilder('taxon')
            ->where('taxon.enabled = true')
            ->setFirstResult($randomOffset)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof TaxonInterface) {
            return $result;
        }

        throw new NoTaxonFoundException();
    }
}
