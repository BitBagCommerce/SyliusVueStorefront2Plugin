<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\Product;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Webmozart\Assert\Assert;

final class ChannelPricingRepository implements ChannelPricingRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findByProductIds(
        array $productIds,
        array $context,
    ): array {
        $channel = $context[ContextKeys::CHANNEL];
        Assert::isInstanceOf($channel, ChannelInterface::class);

         return $this->entityManager->createQueryBuilder()
             ->from(ChannelPricingInterface::class, 'channelPricing')
             ->leftJoin('channelPricing.productVariant', 'variant')
             ->leftJoin('variant.product', 'product')
             ->leftJoin(ChannelInterface::class, 'channel', Join::WITH, 'channel.code = channelPricing.channelCode')
             ->addSelect('channelPricing')
             ->andWhere('product.id IN (:productIds)')
             ->andWhere('channel = :channel')
             ->setParameter('productIds', $productIds)
             ->setParameter('channel', $channel)
             ->getQuery()
             ->getResult();
    }
}
