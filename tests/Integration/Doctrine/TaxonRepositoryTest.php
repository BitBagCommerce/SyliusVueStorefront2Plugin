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
    public function testCreatesChildrenByChannelMenuTaxonQueryBuilder(): void
    {
        $this->loadFixturesFromFile('TaxonRepositoryTest/test_taxon_collection.yml');

        /** @var TaxonRepository $repository */
        $repository = $this->getContainer()->get('sylius.repository.taxon');
        $mainTaxon = $repository->findBy(['parent' => null])[0];

        $qb = $repository->createChildrenByChannelMenuTaxonQueryBuilder($mainTaxon);
        $result = $qb->getQuery()->getResult();

        $this->assertCount(4, $result);

        /**
         * Assert structure:
         *  main
         *      child1
         *          child11
         *      child2
         */

        $this->assertEquals(0, $result[0]->getLevel());
        $this->assertEquals(0, $result[0]->getPosition());

        $this->assertEquals(1, $result[1]->getLevel());
        $this->assertEquals(0, $result[1]->getPosition());

        $this->assertEquals(2, $result[2]->getLevel());
        $this->assertEquals(0, $result[2]->getPosition());

        $this->assertEquals(1, $result[3]->getLevel());
        $this->assertEquals(1, $result[3]->getPosition());
    }
}
