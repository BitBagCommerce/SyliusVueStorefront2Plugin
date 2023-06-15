<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\EntityGenerator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\ProductContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\TaxonContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ChannelPricingFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ProductFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ProductVariantFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\EntityGenerator\ProductGenerator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class ProductGeneratorSpec extends ObjectBehavior
{
    public function let(
        ProductFactoryInterface $productFactory,
        ProductVariantFactoryInterface $productVariantFactory,
        ChannelPricingFactoryInterface $channelPricingFactory,
    ): void {
        $this->beConstructedWith($productFactory, $productVariantFactory, $channelPricingFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductGenerator::class);
    }

    public function it_generates(
        ProductFactoryInterface $productFactory,
        ProductVariantFactoryInterface $productVariantFactory,
        ChannelPricingFactoryInterface $channelPricingFactory,
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant,
        ProductContextInterface $context,
        ProductInterface $product,
    ): void {
        $context->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn(Argument::type('string'));

        $channelPricingFactory
            ->create(Argument::type('integer'), $channel)
            ->willReturn($channelPricing);

        $productVariantFactory
            ->create(
                Argument::type('string'),
                Argument::type('string'),
                $channelPricing->getWrappedObject()
            )
            ->willReturn($productVariant);

        $context->getChannel()->willReturn($channel->getWrappedObject());

        $productFactory
            ->create(
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                $productVariant->getWrappedObject(),
                $channel->getWrappedObject(),
                Argument::type(\DateTime::class),
            )
            ->willReturn($product);

        $this->generate($context);
    }

    public function it_throws_exception_on_invalid_context(TaxonContextInterface $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
