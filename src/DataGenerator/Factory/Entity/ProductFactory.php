<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use DateTimeInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductFactory implements ProductFactoryInterface
{
    private FactoryInterface $productFactory;

    public function __construct(FactoryInterface $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    public function create(
        string $name,
        string $code,
        string $description,
        string $shortDescription,
        ProductVariantInterface $variant,
        ChannelInterface $channel,
        DateTimeInterface $createdAt,
    ): ProductInterface {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setName($name);
        $product->setSlug(Urlizer::transliterate($name));
        $product->setCode($code);
        $product->setDescription($description);
        $product->setShortDescription($shortDescription);
        $product->setEnabled(true);
        $product->setCreatedAt($createdAt);
        $product->addChannel($channel);
        $product->addVariant($variant);

        return $product;
    }
}
