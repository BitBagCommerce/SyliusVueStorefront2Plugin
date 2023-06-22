<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

final class ProductRepository extends EntityRepository implements ProductRepositoryInterface
{
    public function findByChannel(ChannelInterface $channel, int $limit = null, int $offset = null,): array
    {
         return $this->createQueryBuilder('product')
             ->where(':channel MEMBER OF product.channels')
             ->setParameter('channel', $channel)
             ->setFirstResult($offset)
             ->setMaxResults($limit)
             ->getQuery()
             ->getResult();
    }

    public function getEntityCount(ChannelInterface $channel): int
    {
        $queryBuilder = $this->createQueryBuilder('product');
        $queryBuilder
            ->select('COUNT(product)')
            ->where(':channel MEMBER OF product.channels')
            ->setParameter('channel', $channel);

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }
}
