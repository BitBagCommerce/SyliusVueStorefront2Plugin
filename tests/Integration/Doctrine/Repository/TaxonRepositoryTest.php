<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\Doctrine\Repository;

use BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\TaxonRepository;
use Faker\Factory;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;

final class TaxonRepositoryTest extends AbstractRepositoryTest
{
    public function testCreatesChildrenByChannelMenuTaxonQueryBuilder(): void
    {
        # Prepare data
        $mainTaxon = $this->createTaxon();
        $childTaxon1 = $this->createTaxon($mainTaxon);
        $childTaxon11 = $this->createTaxon($childTaxon1);
        $childTaxon111 = $this->createTaxon($childTaxon11);

        $this->entityManager->flush();

        # Test
        /** @var TaxonRepository $repository */
        $repository = $this->getService('sylius.repository.taxon');
        $qb = $repository->createChildrenByChannelMenuTaxonQueryBuilder($mainTaxon);

        $this->assertEquals(3, count($qb->getQuery()->getResult()));
    }

    private function createTaxon(?TaxonInterface $parent = null): TaxonInterface
    {
        $faker = Factory::create();

        $taxon = $this->getService('sylius.factory.taxon')->createNew();
        $taxon->setCode(strtoupper($faker->words(2, true)));
        $taxon->setposition(0);
        if ($parent) {
            $taxon->setParent($parent);
        }

        $translation = new TaxonTranslation();
        $translation->setName(ucfirst($faker->words(2, true)));
        $translation->setSlug($faker->slug);
        $translation->setLocale('en_US');

        $taxon->addTranslation($translation);

        $this->entityManager->persist($taxon);

        return $taxon;
    }
}
