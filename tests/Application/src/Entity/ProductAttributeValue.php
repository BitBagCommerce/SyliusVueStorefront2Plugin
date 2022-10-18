<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Entity;

use BitBag\SyliusVueStorefront2Plugin\Model\ProductAttributeValueTrait;
use Sylius\Component\Product\Model\ProductAttributeValue as BaseProductAttributeValue;

class ProductAttributeValue extends BaseProductAttributeValue
{
    use ProductAttributeValueTrait;
}
