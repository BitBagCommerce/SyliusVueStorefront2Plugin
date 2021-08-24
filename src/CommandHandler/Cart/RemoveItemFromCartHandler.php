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

use BitBag\SyliusGraphqlPlugin\Command\Cart\RemoveItemFromCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class RemoveItemFromCartHandler implements MessageHandlerInterface
{
    private OrderItemRepositoryInterface $orderItemRepository;

    private OrderModifierInterface $orderModifier;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderModifierInterface $orderModifier
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderModifier = $orderModifier;
    }

    public function __invoke(RemoveItemFromCart $removeItemFromCart): OrderInterface
    {
        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $this->orderItemRepository->findOneByIdAndCartTokenValue(
            $removeItemFromCart->itemId,
            $removeItemFromCart->orderTokenValue
        );

        Assert::notNull($orderItem);

        /** @var OrderInterface $cart */
        $cart = $orderItem->getOrder();

        Assert::same($cart->getTokenValue(), $removeItemFromCart->orderTokenValue);

        $this->orderModifier->removeFromOrder($cart, $orderItem);

        return $cart;
    }
}
