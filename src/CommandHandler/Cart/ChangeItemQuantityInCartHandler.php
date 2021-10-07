<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Cart;

use BitBag\SyliusGraphqlPlugin\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChangeItemQuantityInCartHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag_sylius_graphql.change_item_quantity.complete';

    private OrderItemRepositoryInterface $orderItemRepository;

    private OrderItemQuantityModifierInterface $orderItemQuantityModifier;

    private OrderProcessorInterface $orderProcessor;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderProcessor = $orderProcessor;
        $this->eventDispatcher = $eventDispatcher;
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

        $this->eventDispatcher->dispatch(new GenericEvent($cart, [$command]), self::EVENT_NAME);

        return $cart;
    }
}
