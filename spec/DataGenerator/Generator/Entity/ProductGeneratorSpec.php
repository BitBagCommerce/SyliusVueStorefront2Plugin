<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\GeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\ProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\TaxonRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ChannelPricingFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\ProductVariantFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity\ProductGenerator;
use DateTime;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductGeneratorSpec extends ObjectBehavior
{
    public function let(
        ProductFactoryInterface $productFactory,
        ProductVariantFactoryInterface $productVariantFactory,
        ChannelPricingFactoryInterface $channelPricingFactory,
        TaxonRepositoryInterface $taxonRepository,
    ): void {
        $this->beConstructedWith($productFactory, $productVariantFactory, $channelPricingFactory, $taxonRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductGenerator::class);
    }

    public function it_generates_product(
        ProductFactoryInterface $productFactory,
        ProductVariantFactoryInterface $productVariantFactory,
        ChannelPricingFactoryInterface $channelPricingFactory,
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing,
        ProductVariantInterface $productVariant,
        ProductGeneratorContextInterface $context,
        ProductInterface $product,
        TaxonRepositoryInterface $taxonRepository,
        TaxonInterface $taxon,
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
                $channelPricing
            )
            ->willReturn($productVariant);

        $context->getChannel()->willReturn($channel);
        $taxonRepository->getRandomTaxon()->willReturn($taxon);

        $productFactory
            ->create(
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                Argument::type('string'),
                $productVariant,
                $channel,
                Argument::type(DateTime::class),
                $taxon,
            )
            ->willReturn($product);

        $this->generate($context)->shouldReturn($product);
    }

    public function it_throws_exception_on_invalid_context(GeneratorContextInterface $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
