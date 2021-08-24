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

use BitBag\SyliusGraphqlPlugin\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChangeItemQuantityInCartHandler implements MessageHandlerInterface
{
    private OrderItemRepositoryInterface $orderItemRepository;

    private OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    private OrderProcessorInterface $orderProcessor;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(ChangeItemQuantityInCart $command): OrderInterface
    {
        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $this->orderItemRepository->findOneByIdAndCartTokenValue(
            $command->orderItemId,
            $command->orderTokenValue
        );

        Assert::notNull($orderItem);

        /** @var OrderInterface $cart */
        $cart = $orderItem->getOrder();

        Assert::same($cart->getTokenValue(), $command->orderTokenValue);

        $this->orderItemQuantityModifier->modify($orderItem, $command->quantity);
        $this->orderProcessor->process($cart);

        return $cart;
    }
}
