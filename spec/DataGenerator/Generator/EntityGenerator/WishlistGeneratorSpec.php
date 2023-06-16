<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\EntityGenerator;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\EntityContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\ProductContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\EntityContext\WishlistContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ChannelPricingFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ProductFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ProductVariantFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\WishlistFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\EntityGenerator\ProductGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\EntityGenerator\WishlistGenerator;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class WishlistGeneratorSpec extends ObjectBehavior
{
    public function let(WishlistFactoryInterface $wishlistFactory): void
    {
        $this->beConstructedWith($wishlistFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistGenerator::class);
    }

    public function it_generates(
        WishlistFactoryInterface $wishlistFactory,
        WishlistContextInterface $context,
        ChannelInterface $channel,
        WishlistInterface $wishlist,
    ): void {
        $context->getChannel()->willReturn($channel->getWrappedObject());

        $wishlistFactory
            ->create(
                Argument::type('string'),
                Argument::type('string'),
                $channel->getWrappedObject(),
            )
            ->willReturn($wishlist);

        $this->generate($context);
    }

    public function it_throws_exception_on_invalid_context(EntityContextInterface $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
