<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\Product;

use Doctrine\ORM\Query\Expr\Join;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class ProductVariantRepository extends EntityRepository implements ProductVariantRepositoryInterface
{
    public function findByProductIds(
        array $productIds,
        array $context,
    ): array {
        $locale = $context[ContextKeys::LOCALE_CODE] ?? 'en_US';
        $channel = $context[ContextKeys::CHANNEL];
        Assert::isInstanceOf($channel, ChannelInterface::class);

         return $this->createQueryBuilder('variant')
             ->leftJoin('variant.product', 'product')
             ->leftJoin('variant.channelPricings', 'channelPricing')
             ->leftJoin(ChannelInterface::class, 'channel', Join::WITH, 'channel.code = channelPricing.channelCode')
             ->leftJoin('channelPricing.appliedPromotions', 'appliedPromotion')
             ->leftJoin('variant.translations', 'translation')
             ->addSelect('translation')
             ->addSelect('channelPricing')
             ->addSelect('appliedPromotion')
             ->andWhere('product.id IN (:productIds)')
             ->andWhere('translation.locale = :locale')
             ->andWhere('channel = :channel')
             ->setParameter('productIds', $productIds)
             ->setParameter('locale', $locale)
             ->setParameter('channel', $channel)
             ->getQuery()
             ->getResult();
    }

    public function findOptionsByProductIds(
        array $productIds,
        array $context,
    ): array {
        $locale = $context[ContextKeys::LOCALE_CODE] ?? 'en_US';
        $channel = $context[ContextKeys::CHANNEL];
        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $this->createQueryBuilder('variant')
            ->leftJoin('variant.product', 'product')
            ->leftJoin('product.options', 'productOption')
            ->leftJoin('variant.optionValues', 'optionValue')
            ->leftJoin('optionValue.translations', 'optionValueTranslation')
            ->leftJoin('optionValue.option', 'option')
            ->leftJoin('option.translations', 'optionTranslation')
            ->addSelect('product')
            ->addSelect('productOption')
            ->addSelect('optionValue')
            ->addSelect('optionValueTranslation')
            ->addSelect('option')
            ->addSelect('optionTranslation')
            ->andWhere('product.id IN (:productIds)')
            ->andWhere('optionValueTranslation.locale = :locale')
            ->andWhere(':channel MEMBER OF product.channels')
            ->setParameter('productIds', $productIds)
            ->setParameter('locale', $locale)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult();
    }
}
