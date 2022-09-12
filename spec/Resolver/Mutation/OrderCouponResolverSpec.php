<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\Resolver\Mutation;

use BitBag\SyliusGraphqlPlugin\Resolver\Mutation\OrderCouponResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderCouponResolverSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $this->beConstructedWith($orderRepository, $userContext, $eventDispatcher);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderCouponResolver::class);
    }

    function it_is_invokable(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        OrderInterface $order,
        PromotionCouponInterface $promotionCoupon,
        UserInterface $user,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $context = [
            'args' => [
                'input' => [
                    'orderTokenValue' => 'token',
                ],
            ],
        ];

        /** @var array $input */
        $input = $context['args']['input'];
        $orderToken = (string) $input['orderTokenValue'];

        $userContext->getUser()->willReturn($user);

        $orderRepository->findOneBy(['tokenValue' => $orderToken])->willReturn($order);

        $order->getUser()->willReturn($user);

        $order->getPromotionCoupon()->willReturn($promotionCoupon);

        $eventDispatcher->dispatch(Argument::any(), OrderCouponResolver::EVENT_NAME)->shouldBeCalled();

        $this->__invoke(null, $context)->shouldReturn($promotionCoupon);
    }

    function it_throws_an_exception_when_order_nor_found(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        UserInterface $user,
    ): void {
        $context = [
            'args' => [
                'input' => [
                    'orderTokenValue' => 'token',
                ],
            ],
        ];

        $input = $context['args']['input'];
        $orderToken = (string) $input['orderTokenValue'];

        $userContext->getUser()->willReturn($user);

        $orderRepository->findOneBy(['tokenValue' => $orderToken])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [null, $context])
        ;
    }
}
