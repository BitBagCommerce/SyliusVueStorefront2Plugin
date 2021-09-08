<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Resolver\Mutation;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class OrderCouponResolver implements MutationResolverInterface
{
    private OrderRepositoryInterface $orderRepository;

    private UserContextInterface $userContext;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext
    )
    {
        $this->orderRepository = $orderRepository;
        $this->userContext = $userContext;
    }

    public function __invoke($item, array $context)
    {
        if (!isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];

        $orderToken = (string)$input['orderTokenValue'];

        $user = $this->userContext->getUser();

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(["tokenValue" => $orderToken]);

        if (null === $order->getUser() || $user === $order->getUser()) {
            return $order->getPromotionCoupon();
        }

        return null;
    }
}
