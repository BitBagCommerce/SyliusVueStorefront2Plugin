<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductFactory;
use DateTime;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
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

    public function it_creates_product(
        FactoryInterface $productFactory,
        ProductVariantInterface $variant,
        ChannelInterface $channel,
        ProductInterface $product,
        TaxonInterface $taxon,
    ): void {
        $productFactory->createNew()->willReturn($product);

        $this
            ->create(
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                $variant,
                $channel,
                new DateTime(),
                $taxon,
            )
            ->shouldReturn($product);
    }
}
