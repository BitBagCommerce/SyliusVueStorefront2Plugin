<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Model;

use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValue as BaseAttributeValue;

class ProductAttributeValue extends BaseAttributeValue
{

    public function getValue()
    {
        if (null === $this->attribute) {
            return null;
        }

        $getter = 'get' . ucfirst($this->attribute->getStorageType());
        return  $this->$getter();
    }

    /**
     * @return string|null
     */
    public function getStringValue(): ?string
    {
        if (null === $this->attribute) {
            return null;
        }

        $getter = 'get' . ucfirst($this->attribute->getStorageType());

        return (string) $this->$getter();
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
        if(null === $attribute){
            return null;
        }
        $translation = $attribute->getTranslation($this->getLocaleCode());
        return $translation->getName();
    }
}
