<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Doctrine\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepository as BaseInvoiceRepository;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class InvoiceRepository extends BaseInvoiceRepository implements InvoiceRepositoryInterface
{
    public function createInvoiceByUserQueryBuilder(ShopUserInterface $user): QueryBuilder
    {
        return $this
            ->createQueryBuilder('o')
            ->innerJoin('o.order', 'ord')
            ->where('ord.customer = :customer')
            ->setParameter('customer', $user->getCustomer())
            ;
    }

    /**
     * @return InvoiceInterface[]
     */
    public function findAllForOrder(OrderInterface $order): array
    {
        /** @psalm-suppress MixedReturnedTypeCoercion */
        return $this->findBy(['order' => $order]);
    }
}
