<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductFactory;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $productFactory): void
    {
        $this->beConstructedWith($productFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductFactory::class);
    }

    public function it_creates(
        FactoryInterface $productFactory,
        ProductVariantInterface $variant,
        ChannelInterface $channel,
    ): void {
        $name = 'Test product';
        $slug = 'test-product';
        $code = 'TP';
        $description = 'Product created for this test';
        $shortDescription = 'Random product';
        $locale = 'en-US';
        $createdAt = new \DateTime();

        $product = new Product();

        $productFactory->createNew()->willReturn($product);
        $product->setCurrentLocale($locale);
        $product->setName($name);
        $product->setSlug($slug);
        $product->setCode($code);
        $product->setDescription($description);
        $product->setShortDescription($shortDescription);
        $product->setEnabled(true);
        $product->setCreatedAt($createdAt);
        $product->addChannel($channel->getWrappedObject());
        $product->addVariant($variant->getWrappedObject());

        $this->create(
            $name,
            $code,
            $description,
            $shortDescription,
            $variant,
            $channel,
            $createdAt,
        );
    }
}
