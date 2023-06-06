<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\Doctrine;

use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\TaxonRepository;

final class TaxonRepositoryTest extends JsonApiTestCase
{
    public function test_creating_children_by_parent_taxon(): void
    {
        $this->loadFixturesFromFile('TaxonRepositoryTest/test_taxon_collection.yml');

        /** @var TaxonRepository $repository */
        $repository = $this->getContainer()->get('sylius.repository.taxon');
        $mainTaxon = $repository->findBy(['parent' => null])[0];

        $qb = $repository->createChildrenByParentQueryBuilder($mainTaxon);
        $result = $qb->getQuery()->getResult();

        $this->assertCount(4, $result);

        $this->assertEquals('MAIN', $result[0]->getCode());
        $this->assertEquals(0, $result[0]->getLevel());
        $this->assertEquals(0, $result[0]->getPosition());

        $this->assertEquals('CHILD1', $result[1]->getCode());
        $this->assertEquals(1, $result[1]->getLevel());
        $this->assertEquals(0, $result[1]->getPosition());

        $this->assertEquals('CHILD11', $result[2]->getCode());
        $this->assertEquals(2, $result[2]->getLevel());
        $this->assertEquals(0, $result[2]->getPosition());

        $this->assertEquals('CHILD2', $result[3]->getCode());
        $this->assertEquals(1, $result[3]->getLevel());
        $this->assertEquals(1, $result[3]->getPosition());
    }

    public function test_getting_smaller_tree_on_middle_positioned_taxon(): void
    {
        $this->loadFixturesFromFile('TaxonRepositoryTest/test_taxon_collection.yml');

        /** @var TaxonRepository $repository */
        $repository = $this->getContainer()->get('sylius.repository.taxon');
        $mainTaxon = $repository->findBy(['code' => 'CHILD1'])[0];

        $qb = $repository->createChildrenByParentQueryBuilder($mainTaxon);
        $result = $qb->getQuery()->getResult();

        $this->assertCount(2, $result);

        $this->assertEquals('CHILD1', $result[0]->getCode());
        $this->assertEquals(1, $result[0]->getLevel());
        $this->assertEquals(0, $result[0]->getPosition());

        $this->assertEquals('CHILD11', $result[1]->getCode());
        $this->assertEquals(2, $result[1]->getLevel());
        $this->assertEquals(0, $result[1]->getPosition());
    }

    public function test_getting_no_children_for_leaf_taxon(): void
    {
        $this->loadFixturesFromFile('TaxonRepositoryTest/test_taxon_collection.yml');

        /** @var TaxonRepository $repository */
        $repository = $this->getContainer()->get('sylius.repository.taxon');
        $mainTaxon = $repository->findBy(['code' => 'CHILD2'])[0];

        $qb = $repository->createChildrenByParentQueryBuilder($mainTaxon);
        $result = $qb->getQuery()->getResult();

        $this->assertCount(1, $result);

        $this->assertEquals('CHILD2', $result[0]->getCode());
        $this->assertEquals(1, $result[0]->getLevel());
        $this->assertEquals(1, $result[0]->getPosition());
    }
}
