<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\Product;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface as BaseProductVariantRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductVariantRepository extends EntityRepository implements ProductVariantRepositoryInterface
{
    private BaseProductVariantRepositoryInterface $decoratedRepository;

    public function __construct(BaseProductVariantRepositoryInterface $decoratedRepository)
    {
        assert($decoratedRepository instanceof EntityRepository);

        parent::__construct($decoratedRepository->getEntityManager(), $decoratedRepository->getClassMetadata());
        $this->decoratedRepository = $decoratedRepository;
    }

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

    public function createQueryBuilderByProductId(string $locale, $productId): QueryBuilder
    {
        return $this->decoratedRepository->createQueryBuilderByProductId($locale, $productId);
    }

    public function createQueryBuilderByProductCode(string $locale, string $productCode): QueryBuilder
    {
        return $this->decoratedRepository->createQueryBuilderByProductCode($locale, $productCode);
    }

    public function findByName(string $name, string $locale): array
    {
        return $this->decoratedRepository->findByName($name, $locale);
    }

    public function findByNameAndProduct(string $name, string $locale, ProductInterface $product): array
    {
        return $this->decoratedRepository->findByNameAndProduct($name, $locale, $product);
    }

    public function findOneByCodeAndProductCode(string $code, string $productCode): ?ProductVariantInterface
    {
        return $this->decoratedRepository->findOneByCodeAndProductCode($code, $productCode);
    }

    public function findByCodesAndProductCode(array $codes, string $productCode): array
    {
        return $this->decoratedRepository->findByCodesAndProductCode($codes, $productCode);
    }

    public function findByCodes(array $codes): array
    {
        return $this->decoratedRepository->findByCodes($codes);
    }

    public function findOneByIdAndProductId($id, $productId): ?ProductVariantInterface
    {
        return $this->decoratedRepository->findOneByIdAndProductId($id, $productId);
    }

    public function findByPhraseAndProductCode(string $phrase, string $locale, string $productCode): array
    {
        return $this->decoratedRepository->findByPhraseAndProductCode($phrase, $locale, $productCode);
    }

    public function findByPhrase(string $phrase, string $locale, ?int $limit = null): array
    {
        return $this->decoratedRepository->findByPhrase($phrase, $locale, $limit);
    }

    public function getCodesOfAllVariants(): array
    {
        return $this->decoratedRepository->getCodesOfAllVariants();
    }
}
