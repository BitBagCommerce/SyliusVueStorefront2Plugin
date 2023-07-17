<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider\PreFetcher\Product;

interface ProductOptionsPreFetcherInterface
{
    const ELIGIBLE_ATTR_PRODUCT_OPTION_VALUES = 'values';

    const ELIGIBLE_ATTR_PRODUCT_OPTIONS = 'options';

    const ELIGIBLE_ATTR_VARIANT_OPTION_VALUES = 'optionValues';

    const ELIGIBLE_ATTRIBUTES = [
        self::ELIGIBLE_ATTR_PRODUCT_OPTIONS,
        self::ELIGIBLE_ATTR_PRODUCT_OPTION_VALUES,
        self::ELIGIBLE_ATTR_VARIANT_OPTION_VALUES,
    ];
}
