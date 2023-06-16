<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonFactory implements TaxonFactoryInterface
{
    private FactoryInterface $taxonFactory;

    public function __construct(FactoryInterface $taxonFactory)
    {
        $this->taxonFactory = $taxonFactory;
    }

    public function create(
        string $code,
        TaxonInterface $parent,
        TaxonTranslationInterface $translation,
    ): TaxonInterface {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode($code);
        $taxon->setParent($parent);
        $taxon->setPosition($parent->getChildren()->count());
        $taxon->addTranslation($translation);

        return $taxon;
    }
}
