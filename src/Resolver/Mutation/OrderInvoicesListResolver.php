<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Resolver\Mutation;

use BitBag\SyliusGraphqlPlugin\Model\InvoiceUrl;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class OrderInvoicesListResolver
{
    private OrderRepositoryInterface $orderRepository;
    private ObjectRepository $invoiceRepository;
    private UserContextInterface $userContext;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EntityManagerInterface $entityManager,
        UserContextInterface $userContext,
        UrlGeneratorInterface $urlGenerator,
        string $invoiceClass
    ) {
        $this->orderRepository = $orderRepository;
        $this->userContext = $userContext;
        $this->urlGenerator = $urlGenerator;
        $this->invoiceRepository = $entityManager->getRepository($invoiceClass);
    }

    public function __invoke($item, array $context)
    {
        if (!isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];

        $orderToken = (string) $input['orderToken'];

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($orderToken);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order for given token %s has not been found', $orderToken));
        }

        $user = $this->userContext->getUser();
        if ($user === null || $user->getUsername() !== $order->getUser()->getUsername()) {
            throw new AuthenticationException('You are not authenticated to view this resource.');
        }

        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->findOneByOrder($order);

        $invoiceModel = new InvoiceUrl();
        $url = $this->urlGenerator->generate('sylius_invoicing_plugin_shop_invoice_download', [
            'id' => $invoice->id(),
        ]);
        $invoiceModel->addUrl($url);
        $invoiceModel->setId($order->getId());

        return $invoiceModel;
    }
}
