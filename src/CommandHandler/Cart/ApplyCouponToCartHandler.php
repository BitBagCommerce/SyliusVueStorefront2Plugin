<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Cart;

use BitBag\SyliusGraphqlPlugin\Command\Cart\ApplyCouponToCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ApplyCouponToCartHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = "bitbag_sylius_graphql.apply_coupon_to_cart.complete";

    private OrderRepositoryInterface $orderRepository;

    private PromotionCouponRepositoryInterface $promotionCouponRepository;

    private OrderProcessorInterface $orderProcessor;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->orderRepository = $orderRepository;
        $this->promotionCouponRepository = $promotionCouponRepository;
        $this->orderProcessor = $orderProcessor;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ApplyCouponToCart $command): OrderInterface
    {
        Assert::notNull($command->getOrderTokenValue());

        /**
         * @var OrderInterface|null $cart
         * @psalm-suppress PossiblyNullArgument
         */
        $cart = $this->orderRepository->findCartByTokenValue($command->getOrderTokenValue());

        Assert::notNull($cart, 'Cart doesn\'t exist');
        $promotionCoupon = $this->getPromotionCoupon($command->couponCode);

        $cart->setPromotionCoupon($promotionCoupon);

        $this->orderProcessor->process($cart);

        $this->eventDispatcher->dispatch(new GenericEvent($cart,[$command]), self::EVENT_NAME);

        return $cart;
    }

    private function getPromotionCoupon(?string $code): ?PromotionCouponInterface
    {
        if ($code === null) {
            return null;
        }

        /** @var PromotionCouponInterface|null $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $code]);

        Assert::notNull($promotionCoupon, 'Provided code is invalid');

        return $promotionCoupon;
    }
}
