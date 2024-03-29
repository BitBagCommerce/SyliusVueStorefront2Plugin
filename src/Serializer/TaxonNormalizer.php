<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Serializer;

use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class TaxonNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, TaxonInterface::class);

        Assert::keyExists($context, ContextKeys::LOCALE_CODE);
        $locale = $context[ContextKeys::LOCALE_CODE];
        $translation = $object->getTranslation($locale);

        return [
            'id' => $object->getId(),
            'name' => $translation->getName(),
            'code' => $object->getCode(),
            'position' => $object->getPosition(),
            'slug' => $translation->getSlug(),
            'description' => $translation->getDescription(),
            'parent' => is_object($object->getParent())
                ? ['id' => $object->getParent()->getId()]
                : null,
            'enabled' => $object->isEnabled(),
            'level' => $object->getLevel(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof TaxonInterface;
    }
}
