<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Collection;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\WishlistRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk\Collection\WishlistProductCollectionBulkGenerator;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection\WishlistProductCollectionGeneratorInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class WishlistProductCollectionBulkGeneratorSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductCollectionGeneratorInterface $wishlistProductCollectionGenerator,
    ): void {
        $this->beConstructedWith($wishlistRepository, $wishlistProductCollectionGenerator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistProductCollectionBulkGenerator::class);
    }

    public function it_generates_wishlist_product_collection(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductCollectionGeneratorInterface $wishlistProductCollectionGenerator,
        WishlistProductGeneratorContextInterface $context,
        ChannelInterface $channel,
        WishlistInterface $wishlist1,
        WishlistInterface $wishlist2,
        SymfonyStyle $io,
    ): void {
        $entityCount = 2;
        $limit = 100;
        $offset = 0;
        $wishlists = [$wishlist1, $wishlist2];

        $context->getIO()->willReturn($io);
        $context->getChannel()->willReturn($channel);
        $wishlistRepository->getEntityCount($channel)->willReturn($entityCount);

        $wishlistRepository->findByChannel($channel, $limit, $offset)->willReturn($wishlists);

        foreach ($wishlists as $wishlist) {
            $wishlistProductCollectionGenerator->generate($wishlist, $context)->shouldBeCalled();
        }

        $wishlistRepository->findByChannel($channel, $limit, $offset + $limit)->willReturn([]);

        $this->generate($context);
    }

    public function it_does_nothing_if_no_wishlists_found(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductGeneratorContextInterface $context,
        ChannelInterface $channel,
        SymfonyStyle $io,
    ): void {
        $entityCount = 0;
        $limit = 100;
        $offset = 0;

        $context->getIO()->willReturn($io);
        $context->getChannel()->willReturn($channel);
        $wishlistRepository->getEntityCount($channel)->willReturn($entityCount);

        $wishlistRepository->findByChannel($channel, $limit, $offset)->willReturn([]);

        $this->generate($context);
    }

    public function it_throws_exception_on_invalid_context(ContextInterface $context): void
    {
        $this->shouldThrow(InvalidContextException::class)
            ->during('generate', [$context]);
    }
}
