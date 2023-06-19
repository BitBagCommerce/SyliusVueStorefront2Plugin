<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\EntityContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Entity\WishlistContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\UserRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Entity\WishlistFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Entity\WishlistGenerator;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class WishlistGeneratorSpec extends ObjectBehavior
{
    public function let(
        WishlistFactoryInterface $wishlistFactory,
        UserRepositoryInterface $userRepository,
    ): void {
        $this->beConstructedWith($wishlistFactory, $userRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistGenerator::class);
    }

    public function it_generates(
        WishlistFactoryInterface $wishlistFactory,
        UserRepositoryInterface $userRepository,
        WishlistContextInterface $context,
        ChannelInterface $channel,
        ShopUserInterface $shopUser,
        WishlistInterface $wishlist,
    ): void {
        $context->getChannel()->willReturn($channel->getWrappedObject());
        $userRepository->getRandomShopUser()->willReturn($shopUser->getWrappedObject());

        $wishlistFactory
            ->create(
                Argument::type('string'),
                Argument::type('string'),
                $channel->getWrappedObject(),
                $shopUser->getWrappedObject(),
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