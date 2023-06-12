<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TaxonFactory extends Factory implements TaxonFactoryInterface
{
    private FactoryInterface $taxonFactory;
    private TaxonRepositoryInterface $taxonRepository;
    private ?int $maxTaxonLevel = null;
    private ?int $maxChildrenPerTaxonLevel = null;

    public function __construct(
        EntityManagerInterface $entityManager,
        InputInterface $input,
        OutputInterface $output,
        FactoryInterface $taxonFactory,
        TaxonRepositoryInterface $taxonRepository,
    ) {
        parent::__construct($entityManager, $input, $output);

        $this->taxonFactory = $taxonFactory;
        $this->taxonRepository = $taxonRepository;
    }

    public function entityName(): string
    {
        return 'Taxon';
    }

    public function setMaxTaxonLevel(int $maxTaxonLevel): void
    {
        $this->maxTaxonLevel = $maxTaxonLevel;
    }

    public function setMaxChildrenPerTaxonLevel(int $maxChildrenPerTaxonLevel): void
    {
        $this->maxChildrenPerTaxonLevel = $maxChildrenPerTaxonLevel;
    }

    public function create(): TaxonInterface
    {
        $uuid = $this->faker->uuid;
        $parent = $this->getParentTaxon($this->maxTaxonLevel, $this->maxChildrenPerTaxonLevel);

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
        ?int $maxTaxonLevel,
        ?int $maxChildrenPerTaxonLevel,
    ): TaxonInterface {
        if ($maxTaxonLevel === null || $maxChildrenPerTaxonLevel === null) {
            return $this->taxonRepository->getMainTaxon();
        }

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
