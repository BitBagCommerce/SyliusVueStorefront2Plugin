<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\DataGenerator;

use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepository;

final class TaxonRepositoryTest extends JsonApiTestCase
{
    public function test_getting_main_taxon(): void
    {
        $this->loadFixturesFromFile('DataGenerator/taxon_repository.yml');

        /** @var TaxonRepository $repository */
        $repository = $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.taxon_repository');

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

        $this->loadFixturesFromFile('DataGenerator/taxon_repository.yml');

        /** @var TaxonRepository $repository */
        $repository = $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.taxon_repository');

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

        $this->loadFixturesFromFile('DataGenerator/taxon_repository.yml');

        /** @var TaxonRepository $repository */
        $repository = $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.taxon_repository');

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

        $this->loadFixturesFromFile('DataGenerator/taxon_repository.yml');

        /** @var TaxonRepository $repository */
        $repository = $this->getContainer()
            ->get('bitbag.sylius_vue_storefront2_plugin.data_generator.repository.taxon_repository');

        $eligibleParents = $repository->findEligibleParents($maxTaxonLevel, $maxChildrenPerTaxonLevel);

        $this->assertCount(0, $eligibleParents);
    }
}
