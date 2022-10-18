<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Model;

use Sylius\Component\Attribute\Model\AttributeInterface;
use Webmozart\Assert\Assert;

trait ProductAttributeValueTrait
{
    /** @return mixed|null */
    public function getValue()
    {
        if (null === $this->attribute) {
            return null;
        }

        $storageType = $this->attribute->getStorageType();
        Assert::notNull($storageType);

        $getter = 'get' . ucfirst($storageType);

        if (method_exists($this, $getter)) {
            /** @var callable $callback */
            $callback = [$this, $getter];

            return call_user_func($callback);
        }

        return null;
    }

    public function getStringValue(): ?string
    {
        return (string) $this->getValue();
    }

    public function getAttribute(): ?AttributeInterface
    {
        return parent::getAttribute();
    }

    public function getLocaleCode(): ?string
    {
        return parent::getLocaleCode();
    }

    public function getCode(): ?string
    {
        return parent::getCode();
    }

    public function getName(): ?string
    {
        $attribute = $this->getAttribute();
        if (null === $attribute) {
            return null;
        }
        $translation = $attribute->getTranslation($this->getLocaleCode());

        return $translation->getName();
    }
}
