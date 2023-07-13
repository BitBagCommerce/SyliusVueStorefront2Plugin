<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\DataGenerator;

use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepositoryInterface;

final class TaxonRepositoryTest extends JsonApiTestCase
{
    public function test_getting_main_taxon(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();

        $mainTaxon = $repository->getMainTaxon();

        $this->assertEquals('MAIN', $mainTaxon->getCode());
        $this->assertNull($mainTaxon->getParent());
        $this->assertEquals(0, $mainTaxon->getLevel());
        $this->assertEquals(0, $mainTaxon->getPosition());
    }

    public function test_finding_sorted_eligible_parents(): void
    {
        $maxTaxonLevel = 3;
        $maxChildrenPerTaxonLevel = 2;

        $this->loadFixtures();
        $repository = $this->getRepository();

        $eligibleParents = $repository->findEligibleParents($maxTaxonLevel, $maxChildrenPerTaxonLevel);

        $this->assertCount(4, $eligibleParents);
        $this->assertSame('CHILD11', $eligibleParents[0]->getCode());
        $this->assertSame(2, $eligibleParents[0]->getLevel());
        $this->assertSame(3, $eligibleParents[0]->getLeft());
        $this->assertSame('CHILD12', $eligibleParents[1]->getCode());
        $this->assertSame(2, $eligibleParents[1]->getLevel());
        $this->assertSame(7, $eligibleParents[1]->getLeft());
        $this->assertSame('CHILD2', $eligibleParents[2]->getCode());
        $this->assertSame(1, $eligibleParents[2]->getLevel());
        $this->assertSame(10, $eligibleParents[2]->getLeft());
        $this->assertSame('CHILD3', $eligibleParents[3]->getCode());
        $this->assertSame(1, $eligibleParents[3]->getLevel());
        $this->assertSame(12, $eligibleParents[3]->getLeft());
    }

    public function test_finding_limited_eligible_parents(): void
    {
        $maxTaxonLevel = 3;
        $maxChildrenPerTaxonLevel = 3;
        $limit = 2;

        $this->loadFixtures();
        $repository = $this->getRepository();

        $eligibleParents = $repository->findEligibleParents($maxTaxonLevel, $maxChildrenPerTaxonLevel, $limit);

        $this->assertCount(2, $eligibleParents);
        $this->assertSame('CHILD11', $eligibleParents[0]->getCode());
        $this->assertSame(2, $eligibleParents[0]->getLevel());
        $this->assertSame(3, $eligibleParents[0]->getLeft());
        $this->assertSame('CHILD12', $eligibleParents[1]->getCode());
        $this->assertSame(2, $eligibleParents[1]->getLevel());
        $this->assertSame(7, $eligibleParents[1]->getLeft());
    }

    public function test_not_finding_eligible_parents(): void
    {
        $maxTaxonLevel = 1;
        $maxChildrenPerTaxonLevel = 1;

        $this->loadFixtures();
        $repository = $this->getRepository();

        $eligibleParents = $repository->findEligibleParents($maxTaxonLevel, $maxChildrenPerTaxonLevel);

        $this->assertCount(0, $eligibleParents);
    }

    public function test_find_batch_with_limit(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();

        $taxons = $repository->findBatch(2);

        $this->assertCount(2, $taxons);

        $this->assertSame('MAIN', $taxons[0]->getCode());
        $this->assertSame('CHILD1', $taxons[1]->getCode());
    }

    public function test_find_batch_with_offset(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();

        $taxons = $repository->findBatch(null, 5);

        $this->assertCount(2, $taxons);

        $this->assertSame('CHILD2', $taxons[0]->getCode());
        $this->assertSame('CHILD3', $taxons[1]->getCode());
    }

    public function test_find_batch_with_limit_and_offset(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();

        $taxons = $repository->findBatch(3, 2);

        $this->assertCount(3, $taxons);

        $this->assertSame('CHILD11', $taxons[0]->getCode());
        $this->assertSame('CHILD111', $taxons[1]->getCode());
        $this->assertSame('CHILD12', $taxons[2]->getCode());
    }

    public function test_find_batch_with_offset_exceeded(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();

        $taxons = $repository->findBatch(2, 20);

        $this->assertCount(0, $taxons);
    }

    public function test_getting_entity_count(): void
    {
        $this->loadFixtures();
        $repository = $this->getRepository();

        $entityCount = $repository->getEntityCount();

        $this->assertEquals(7, $entityCount);
    }

    private function loadFixtures(): void
    {
        $this->loadFixturesFromFile('DataGenerator/taxon_repository.yml');
    }

    private function getRepository(): TaxonRepositoryInterface
    {
        return $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.taxon_repository');
    }
}
