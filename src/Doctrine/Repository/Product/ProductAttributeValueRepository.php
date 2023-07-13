<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\Product;

use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface as BaseProductAttributeValueRepositoryInterface;

final class ProductAttributeValueRepository extends EntityRepository implements ProductAttributeValueRepositoryInterface
{
    private BaseProductAttributeValueRepositoryInterface $decoratedRepository;

    public function __construct(BaseProductAttributeValueRepositoryInterface $decoratedRepository)
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

         return $this->decoratedRepository->createQueryBuilder('value')
             ->leftJoin('value.attribute', 'attribute')
             ->leftJoin('attribute.translations', 'translation')
             ->leftJoin('value.subject', 'product')
             ->addSelect('attribute')
             ->addSelect('translation')
             ->andWhere('product.id IN (:productIds)')
             ->andWhere('translation.locale = :locale')
             ->setParameter('productIds', $productIds)
             ->setParameter('locale', $locale)
             ->getQuery()
             ->getResult();
    }

    public function findByJsonChoiceKey(string $choiceKey): array
    {
        return $this->decoratedRepository->findByJsonChoiceKey($choiceKey);
    }
}
