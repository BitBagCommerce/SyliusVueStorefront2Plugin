<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository;

use Sylius\Component\Core\Model\TaxonInterface;

interface TaxonRepositoryInterface
{
    const DEFAULT_LIMIT = 20;

    public function getMainTaxon(): TaxonInterface;

    /**
     * @return array<TaxonInterface>
     */
    public function findEligibleParents(
        int $maxTaxonLevel,
        int $maxChildrenPerTaxonLevel,
        int $limit = self::DEFAULT_LIMIT,
    ): array;
}
