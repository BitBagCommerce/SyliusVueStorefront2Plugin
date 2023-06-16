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

use Gedmo\Sluggable\Util\Urlizer;
use Sylius\Component\Taxonomy\Model\TaxonTranslation;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

final class TaxonTranslationFactory implements TaxonTranslationFactoryInterface
{
    public static function create(
        string $name,
        string $locale,
    ): TaxonTranslationInterface {
        $translation = new TaxonTranslation();
        $translation->setName($name);
        $translation->setSlug(Urlizer::transliterate($name));
        $translation->setLocale($locale);

        return $translation;
    }
}
