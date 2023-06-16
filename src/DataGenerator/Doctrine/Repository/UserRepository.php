<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Doctrine\Repository;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository as BaseUserRepository;
use Sylius\Component\Core\Model\ShopUserInterface;

final class UserRepository extends BaseUserRepository implements UserRepositoryInterface
{
    public function getRandomShopUser(): ShopUserInterface
    {
        $userCount = (int)$this->createQueryBuilder('u')
            ->innerJoin('u.customer', 'customer')
            ->select('COUNT(u)')
            ->getQuery()
            ->getSingleScalarResult();

        $randomOffset = rand(0, $userCount - 1);

        return $this->createQueryBuilder('u')
            ->innerJoin('u.customer', 'customer')
            ->setFirstResult($randomOffset)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
