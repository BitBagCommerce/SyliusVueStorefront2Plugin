<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Cart;

use BitBag\SyliusGraphqlPlugin\Command\Cart\ApplyCouponToCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ApplyCouponToCartHandler implements MessageHandlerInterface
{
    private OrderRepositoryInterface $orderRepository;

    private PromotionCouponRepositoryInterface $promotionCouponRepository;

    private OrderProcessorInterface $orderProcessor;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderRepository = $orderRepository;
        $this->promotionCouponRepository = $promotionCouponRepository;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(ApplyCouponToCart $command): OrderInterface
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findCartByTokenValue($command->getOrderTokenValue());

        Assert::notNull($cart, 'Cart doesn\'t exist');
        $promotionCoupon = $this->getPromotionCoupon($command->couponCode);

        $cart->setPromotionCoupon($promotionCoupon);

        $this->orderProcessor->process($cart);

        return $cart;
    }

    private function getPromotionCoupon(?string $code): ?PromotionCouponInterface
    {
        if ($code === null) {
            return null;
        }

        /** @var PromotionCouponInterface $promotionCoupon */
        $promotionCoupon = $this->promotionCouponRepository->findOneBy(['code' => $code]);

        Assert::notNull($promotionCoupon);

        return $promotionCoupon;
    }
}
