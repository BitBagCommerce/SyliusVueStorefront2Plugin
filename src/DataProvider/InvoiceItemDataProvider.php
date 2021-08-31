<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

/** @experimental */
final class InvoiceItemDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    private UserContextInterface $userContext;

    private ObjectRepository $invoiceRepository;
    private string $invoiceClass;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserContextInterface $userContext,
        string $invoiceClass
    )
    {
        $this->userContext = $userContext;
        $this->invoiceClass = $invoiceClass;
        $this->invoiceRepository = $entityManager->getRepository($invoiceClass);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userContext->getUser();
die('sad');
        $invoice = $this->invoiceRepository->find($id);
        /** @var OrderInterface $order */
        $order = $invoice->order();
        if (
            $user instanceof ShopUserInterface &&
            $order->getUser()->getId() === $user->getId()
        ) {
            return $invoice;
        }

        return null;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, $this->invoiceClass, true);
    }
}
