<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
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
    )
    {
        $this->orderRepository = $orderRepository;
        $this->userContext = $userContext;
        $this->urlGenerator = $urlGenerator;
        $this->invoiceRepository = $entityManager->getRepository($invoiceClass);
    }

    public function __invoke($item, $context)
    {
        if (!is_array($context) || !isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];

        $orderToken = (string) $input['orderToken'];

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($orderToken);

        if(null === $order){
            throw new NotFoundHttpException(sprintf("Order for given token %s has not been found", $orderToken));
        }

        $user = $this->userContext->getUser();
        if ($user === null || $user->getUsername() !== $order->getUser()->getUsername()) {
            throw new AuthenticationException("You are not authenticated to view this resource.");
        }

        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->findOneByOrder($order);

        $invoiceModel = new InvoiceUrl();
        $url = $this->urlGenerator->generate("sylius_invoicing_plugin_shop_invoice_download", [
            "id" => $invoice->id()
        ]);
        $invoiceModel->addUrl($url);
        $invoiceModel->setId($order->getId());

        return $invoiceModel;
    }
}
