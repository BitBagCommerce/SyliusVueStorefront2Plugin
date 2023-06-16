<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductVariantFactory;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductVariantFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $productVariantFactory): void
    {
        $this->beConstructedWith($productVariantFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductVariantFactory::class);
    }

    public function it_creates(
        FactoryInterface $productVariantFactory,
        ChannelPricingInterface $channelPricing,
    ): void {
        $name = 'Test product variant';
        $code = 'TPV';
        $locale = 'en-US';

        $productVariant = new ProductVariant();

        $productVariantFactory->createNew()->willReturn($productVariant);
        $productVariant->setCurrentLocale($locale);
        $productVariant->setName($name);
        $productVariant->setCode($code);
        $productVariant->addChannelPricing($channelPricing->getWrappedObject());

        $this->create($name, $code, $channelPricing);
    }
}
