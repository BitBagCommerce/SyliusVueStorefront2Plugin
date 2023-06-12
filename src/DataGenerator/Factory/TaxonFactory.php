<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepositoryInterface;
use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonFactory implements TaxonFactoryInterface
{
    private FactoryInterface $taxonFactory;
    private TaxonRepositoryInterface $taxonRepository;
    private Generator $faker;

    public function __construct(
        FactoryInterface $taxonFactory,
        TaxonRepositoryInterface $taxonRepository,
    ) {
        $this->taxonFactory = $taxonFactory;
        $this->taxonRepository = $taxonRepository;

        $this->faker = Factory::create();
    }

    public function create(
        int $maxTaxonLevel,
        int $maxChildrenPerTaxonLevel,
    ): TaxonInterface {
        $uuid = $this->faker->uuid;
        $parent = $this->getParentTaxon($maxTaxonLevel, $maxChildrenPerTaxonLevel);

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode('code-' . $uuid);
        $taxon->setParent($parent);
        $taxon->setPosition($parent->getChildren()->count());
        $taxon->addTranslation($this->createTranslation($uuid));

        return $taxon;
    }

    private function createTranslation(string $uuid): TaxonTranslationInterface
    {
        $translation = new TaxonTranslation();
        $translation->setName($uuid);
        $translation->setSlug($uuid);
        $translation->setLocale(self::DEFAULT_LOCALE);

        return $translation;
    }

    private function getParentTaxon(
        int $maxTaxonLevel,
        int $maxChildrenPerTaxonLevel,
    ): TaxonInterface {
        $eligibleParents = $this->taxonRepository->findEligibleParents(
            $maxTaxonLevel,
            $maxChildrenPerTaxonLevel
        );

        if (count($eligibleParents) === 0) {
            return $this->taxonRepository->getMainTaxon();
        }

        return $eligibleParents[array_rand($eligibleParents)];
    }
}
