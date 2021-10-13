<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Cart;

use BitBag\SyliusGraphqlPlugin\Command\Cart\RemoveCouponFromCart;
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
final class RemoveCouponFromCartHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag_sylius_graphql.remove_coupon_from_cart.complete';

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

    public function __invoke(RemoveCouponFromCart $command): OrderInterface
    {
        $tokenValue = $command->getOrderTokenValue();
        Assert::notNull($tokenValue);

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($tokenValue);

        Assert::notNull($cart, 'Cart doesn\'t exist');

        $promotionCoupon = $this->getPromotionCoupon($command->couponCode);
        Assert::notNull($promotionCoupon);

        $promotion = $promotionCoupon->getPromotion();

        Assert::notNull($promotion);
        $cart->removePromotion($promotion);

        $cartCoupon = $cart->getPromotionCoupon();
        if ($this->shouldCouponBeRemovedFromCart($cartCoupon, $command->couponCode)) {
            $cart->setPromotionCoupon(null);
        }
        $this->orderProcessor->process($cart);

        /** @psalm-suppress TooManyArguments */
        $this->eventDispatcher->dispatch(new GenericEvent($cart, [$command]), self::EVENT_NAME);

        return $cart;
    }

    private function shouldCouponBeRemovedFromCart(?PromotionCouponInterface $cartCoupon, string $code): bool
    {
        if (null === $cartCoupon) {
            return false;
        }

        if ($cartCoupon->getCode() !== $code) {
            return false;
        }

        return true;
    }

    private function getPromotionCoupon(?string $code): ?PromotionCouponInterface
    {
        if ($code === null) {
            return null;
        }

        /** @var PromotionCouponInterface|null $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $code]);

        Assert::notNull($promotionCoupon);

        return $promotionCoupon;
    }
}
