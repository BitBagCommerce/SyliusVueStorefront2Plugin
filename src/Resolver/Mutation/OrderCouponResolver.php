<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Resolver\Mutation;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

final class OrderCouponResolver implements MutationResolverInterface
{
    public const EVENT_NAME = 'bitbag.sylius_graphql.mutation_resolver.order_coupon.complete';

    private OrderRepositoryInterface $orderRepository;

    private UserContextInterface $userContext;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->orderRepository = $orderRepository;
        $this->userContext = $userContext;
        $this->eventDispatcher = $eventDispatcher;
    }

    /** @param object|null $item */
    public function __invoke($item, array $context): ?PromotionCouponInterface
    {
        if (!isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];

        $orderToken = (string) $input['orderTokenValue'];

        $user = $this->userContext->getUser();

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $orderToken]);
        Assert::notNull($order);

        /** @psalm-suppress TooManyArguments */
        $this->eventDispatcher->dispatch(new GenericEvent($order, $input), self::EVENT_NAME);

        if ($this->isAllowedToRetrieveCoupon($order, $user)) {
            return $order->getPromotionCoupon();
        }

        return null;
    }

    private function isAllowedToRetrieveCoupon(OrderInterface $order, ?UserInterface $user): bool
    {
        return null === $order->getUser() || $user === $order->getUser();
    }
}
