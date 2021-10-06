<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\CommandHandler\Cart;

use BitBag\SyliusGraphqlPlugin\Command\Cart\ApplyCouponToCart;
use BitBag\SyliusGraphqlPlugin\Command\Cart\ApplyCouponToCartSpec;
use BitBag\SyliusGraphqlPlugin\CommandHandler\Cart\ApplyCouponToCartHandler;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;


final class ApplyCouponToCartHandlerSpec extends ObjectBehavior
{

    function let(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor
    ): void
    {
        $this->beConstructedWith($orderRepository, $promotionCouponRepository, $orderProcessor);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ApplyCouponToCartHandler::class);
    }

    function it_is_invokable(
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        PromotionCouponInterface $promotionCoupon
    ): void
    {
        $code = "code";
        $command = new ApplyCouponToCart($code, "token");

        $orderRepository->findCartByTokenValue((string)$command->getOrderTokenValue())->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => $code])->willReturn($promotionCoupon);

        $cart->setPromotionCoupon($promotionCoupon->getWrappedObject())->shouldBeCalled();
        $orderProcessor->process($cart->getWrappedObject())->shouldBeCalled();

        $this->__invoke($command);
    }

    function it_throws_an_exception_when_could_not_found_cart(
        OrderRepositoryInterface $orderRepository
    ): void
    {
        $command = new ApplyCouponToCart("code", "token");

        $orderRepository->findCartByTokenValue((string)$command->getOrderTokenValue())->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command]);
    }

}
