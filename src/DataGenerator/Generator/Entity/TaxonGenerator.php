<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\EntityContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\TaxonContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\TaxonFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\TaxonTranslationFactory;
use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Model\TaxonInterface;

final class TaxonGenerator implements GeneratorInterface
{
    private TaxonFactoryInterface $taxonFactory;

    private TaxonRepositoryInterface $taxonRepository;

    private Generator $faker;

    public function __construct(
        TaxonFactoryInterface $taxonFactory,
        TaxonRepositoryInterface $taxonRepository,
    ) {
        $this->taxonFactory = $taxonFactory;
        $this->taxonRepository = $taxonRepository;
        $this->faker = Factory::create();
    }

    public function generate(EntityContextInterface $context): TaxonInterface
    {
        if (!$context instanceof TaxonContextInterface) {
            throw new InvalidContextException();
        }

        $translation = TaxonTranslationFactory::create(
            $this->faker->sentence(3),
            $context::DEFAULT_LOCALE,
        );

        return $this->taxonFactory->create(
            $this->faker->uuid,
            $this->findParentTaxon($context),
            $translation,
        );
    }

    private function findParentTaxon(TaxonContextInterface $context): TaxonInterface {
        $maxTaxonLevel = $context->getMaxTaxonLevel();
        $maxChildrenPerTaxonLevel = $context->getMaxChildrenPerTaxonLevel();

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
