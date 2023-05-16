<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository;

use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface as BaseTaxonRepositoryInterface;

final class TaxonRepository extends NestedTreeRepository implements TaxonRepositoryInterface, BaseTaxonRepositoryInterface
{
    use ResourceRepositoryTrait;

    public function __construct(private BaseTaxonRepositoryInterface $decoratedRepository)
    {
        parent::__construct($this->decoratedRepository->_em, $decoratedRepository->_class);
    }

    public function createChildrenByChannelMenuTaxonQueryBuilder(
        ?TaxonInterface $menuTaxon = null,
        ?string $locale = null,
    ): QueryBuilder {
        $qb = $this->childrenQueryBuilder($menuTaxon, false, 'position', 'asc');
        $alias = $qb->getRootAliases()[0] ?? 'node';

        return $this->addTranslations($qb, $alias, $locale)
            ->andWhere($alias . '.enabled = :enabled')
            ->setParameter('enabled', true);
    }

    public function findChildren(string $parentCode, ?string $locale = null): array
    {
        return $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('child')
            ->innerJoin('o.parent', 'parent')
            ->leftJoin('o.children', 'child')
            ->andWhere('parent.code = :parentCode')
            ->addOrderBy('o.position')
            ->setParameter('parentCode', $parentCode)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findChildrenByChannelMenuTaxon(?TaxonInterface $menuTaxon = null, ?string $locale = null): array
    {
        $hydrationQuery = $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('o')
            ->addSelect('oc')
            ->leftJoin('o.children', 'oc')
        ;

        if (null !== $menuTaxon) {
            $hydrationQuery
                ->andWhere('o.root = :root')
                ->setParameter('root', $menuTaxon)
            ;
        }

        $hydrationQuery->getQuery()->getResult();

        return $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('child')
            ->innerJoin('o.parent', 'parent')
            ->leftJoin('o.children', 'child')
            ->andWhere('o.enabled = :enabled')
            ->andWhere('parent.code = :parentCode')
            ->addOrderBy('o.position')
            ->setParameter('parentCode', ($menuTaxon !== null) ? $menuTaxon->getCode() : 'category')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findRootNodes(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.parent IS NULL')
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findHydratedRootNodes(): array
    {
        $this->createQueryBuilder('o')
            ->select(['o', 'oc', 'ot'])
            ->leftJoin('o.children', 'oc')
            ->leftJoin('o.translations', 'ot')
            ->getQuery()
            ->getResult()
        ;

        return $this->findRootNodes();
    }

    public function findOneBySlug(string $slug, string $locale): ?TaxonInterface
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('o.enabled = :enabled')
            ->andWhere('translation.slug = :slug')
            ->andWhere('translation.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->setParameter('enabled', true)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findByName(string $name, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByNamePart(string $phrase, ?string $locale = null, ?int $limit = null): array
    {
        /** @var TaxonInterface[] $results */
        $results = $this->createTranslationBasedQueryBuilder($locale)
            ->andWhere('translation.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        foreach ($results as $result) {
            $result->setFallbackLocale(array_key_first($result->getTranslations()->toArray()));
        }

        return $results;
    }

    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')->leftJoin('o.translations', 'translation');
    }

    private function createTranslationBasedQueryBuilder(?string $locale): QueryBuilder
    {
        $alias = 'o';
        $queryBuilder = $this->createQueryBuilder($alias);

        return $this->addTranslations($queryBuilder, $alias, $locale);
    }

    private function addTranslations(QueryBuilder $queryBuilder, string $alias, ?string $locale): QueryBuilder
    {
        $queryBuilder
            ->addSelect('translation')
            ->leftJoin($alias . '.translations', 'translation')
        ;

        if (null !== $locale) {
            $queryBuilder
                ->andWhere('translation.locale = :locale')
                ->setParameter('locale', $locale)
            ;
        }

        return $queryBuilder;
    }
}
