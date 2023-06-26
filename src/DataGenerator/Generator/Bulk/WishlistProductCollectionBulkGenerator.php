<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Bulk;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\ContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\Generator\WishlistProductGeneratorContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository\WishlistRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Exception\InvalidContextException;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Generator\Collection\WishlistProductCollectionGeneratorInterface;
use DateTime;

final class WishlistProductCollectionBulkGenerator implements WishlistProductCollectionBulkGeneratorInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private WishlistProductCollectionGeneratorInterface $wishlistProductCollectionGenerator;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistProductCollectionGeneratorInterface $wishlistProductCollectionGenerator,
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductCollectionGenerator = $wishlistProductCollectionGenerator;
    }

    public function generate(ContextInterface $context): void
    {
        if (!$context instanceof WishlistProductGeneratorContextInterface) {
            throw new InvalidContextException();
        }

        $io = $context->getIO();

        $io->info(sprintf(
            '%s Generating WishlistProducts',
            (new DateTime())->format('Y-m-d H:i:s'),
        ));

        $channel = $context->getChannel();
        $offset = 0;

        $io->progressStart($this->wishlistRepository->getEntityCount($channel));

        while (
            count($wishlists = $this->wishlistRepository->findByChannel($channel, self::LIMIT, $offset)) > 0
        ) {
            foreach ($wishlists as $wishlist) {
                $this->wishlistProductCollectionGenerator->generate($wishlist, $context);
                $io->progressAdvance();
            }

            $offset += self::LIMIT;
        }

        $io->progressFinish();
    }
}
