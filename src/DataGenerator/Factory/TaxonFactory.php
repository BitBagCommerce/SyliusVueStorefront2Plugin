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

final class TaxonFactory
{
    private FactoryInterface $taxonFactory;

    public function __construct(
        FactoryInterface $taxonFactory,
    ) {

        $this->taxonFactory = $taxonFactory;
    }

    public function entityName(): string
    {
        return 'Taxon';
    }

    public function create($uuid, TaxonInterface $parent): TaxonInterface
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode('code-' . $uuid);
        $taxon->setParent($parent);
        $taxon->setPosition($parent->getChildren()->count());
        $taxon->addTranslation($this->createTranslation($uuid));

        return $taxon;
    }

    private function createTranslation(string $uuid, string $locale): TaxonTranslationInterface
    {
        $translation = new TaxonTranslation();
        $translation->setName($uuid);
        $translation->setSlug($uuid);
        $translation->setLocale($locale);

        return $translation;
    }
}
