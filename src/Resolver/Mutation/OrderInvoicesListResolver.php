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


use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class OrderInvoicesListResolver
{
    private OrderRepositoryInterface $orderRepository;
    private EntityManagerInterface $entityManager;
    private UserContextInterface $userContext;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EntityManagerInterface $entityManager,
        UserContextInterface $userContext
    )
    {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
        $this->userContext = $userContext;
    }

    public function __invoke($item, $context)
    {
        if (!is_array($context) || !isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];

        $orderId = (int) $input['orderId'];

        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($orderId);

        $user = $this->userContext->getUser();
        if ($user === null || $user->getUserIdentifier() !== $order->getUser()->getUserIdentifier()) {
            throw new AuthenticationException();
        }

    }
}
