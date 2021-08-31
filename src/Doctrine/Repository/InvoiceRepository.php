<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Doctrine\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepository as BaseInvoiceRepository;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;

final class InvoiceRepository extends BaseInvoiceRepository implements InvoiceRepositoryInterface
{
    public function createInvoiceByUserQueryBuilder(ShopUserInterface $user): QueryBuilder
    {
        return $this
            ->createQueryBuilder('o')
            ->innerJoin('o.order', 'ord')
            ->where('ord.customer = :customer')
            ->setParameter('customer', $user->getCustomer());

    }
}
