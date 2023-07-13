<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\Product;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductImageInterface;

final class ProductImageRepository implements ProductImageRepositoryInterface
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
        $fields = array_map(
            static fn(string $field) => 'image.' . $field,
            array_keys($context['attributes']['images']['collection'] ?? []),
        );
        $fields = implode(', ', $fields);

         return $this->entityManager->createQueryBuilder()
             ->from(ProductImageInterface::class, 'image')
             ->join('image.owner', 'product')
             ->select($fields)
             ->addSelect('product.code')
             ->andWhere('product.id IN (:productIds)')
             ->setParameter('productIds', $productIds)
             ->getQuery()
             ->getResult();
    }
}
