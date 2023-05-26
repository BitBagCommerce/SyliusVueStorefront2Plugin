<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface as BaseTaxonRepositoryInterface;

final class TaxonRepository extends NestedTreeRepository implements TaxonRepositoryInterface
{
    use ResourceRepositoryTrait;

    public function __construct(private BaseTaxonRepositoryInterface $decoratedRepository)
    {
        assert($decoratedRepository instanceof EntityRepository);
        parent::__construct($decoratedRepository->getEntityManager(), $decoratedRepository->getClassMetadata());
    }

    public function createChildrenByChannelMenuTaxonQueryBuilder(
        ?TaxonInterface $menuTaxon = null,
        ?string $locale = null,
    ): QueryBuilder {
        $qb = $this->childrenQueryBuilder($menuTaxon, false, null, null, true);

        $alias = $qb->getRootAliases()[0] ?? 'node';

        $this->addTranslations($qb, $alias, $locale);

        return $qb->andWhere($alias . '.enabled = :enabled')
            ->setParameter('enabled', true);
    }

    public function findChildren(string $parentCode, ?string $locale = null): array
    {
        return $this->decoratedRepository->findChildren($parentCode, $locale);
    }

    public function findChildrenByChannelMenuTaxon(?TaxonInterface $menuTaxon = null, ?string $locale = null): array
    {
        return $this->decoratedRepository->findChildrenByChannelMenuTaxon($menuTaxon, $locale);
    }

    public function findRootNodes(): array
    {
        return $this->decoratedRepository->findRootNodes();
    }

    public function findHydratedRootNodes(): array
    {
        return $this->decoratedRepository->findHydratedRootNodes();
    }

    public function findOneBySlug(string $slug, string $locale): ?TaxonInterface
    {
        return $this->decoratedRepository->findOneBySlug($slug, $locale);
    }

    public function findByName(string $name, string $locale): array
    {
        return $this->decoratedRepository->findByName($name, $locale);
    }

    public function findByNamePart(string $phrase, ?string $locale = null, ?int $limit = null): array
    {
        return $this->decoratedRepository->findByNamePart($phrase, $locale, $limit);
    }

    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->decoratedRepository->createListQueryBuilder();
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
