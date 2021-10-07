<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\BillingAddressOrder;
use BitBag\SyliusGraphqlPlugin\Resolver\OrderAddressStateResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class BillingAddressOrderHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag_sylius_graphql.choose_order_billing_address.complete';

    private OrderRepositoryInterface $orderRepository;

    private ObjectManager $manager;

    private CustomerProviderInterface $customerProvider;

    private OrderAddressStateResolverInterface $addressStateResolver;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $manager,
        CustomerProviderInterface $customerProvider,
        OrderAddressStateResolverInterface $addressStateResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->orderRepository = $orderRepository;
        $this->manager = $manager;
        $this->customerProvider = $customerProvider;
        $this->addressStateResolver = $addressStateResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(BillingAddressOrder $command): OrderInterface
    {
        $tokenValue = $command->orderTokenValue;
        Assert::notNull($tokenValue);

        $order = $this->orderRepository->findCartByTokenValue($tokenValue);
        Assert::isInstanceOf($order, OrderInterface::class, sprintf('Order with %s token has not been found.', $tokenValue));

        $this->applyCustomer($order, $command);

        $this->applyBillingAddress($command, $order);

        $this->addressStateResolver->resolve($order);

        $this->manager->persist($order);

        $this->eventDispatcher->dispatch(new GenericEvent($order, [$command]), self::EVENT_NAME);

        return $order;
    }

    private function shouldSetCustomer(OrderInterface $order, BillingAddressOrder $addressOrder): bool
    {
        return null === $order->getCustomer() && null !== $addressOrder->email;
    }

    private function canAddressBeSet(BillingAddressOrder $addressOrder): bool
    {
        return $addressOrder->billingAddress !== null && $addressOrder->billingAddress instanceof AddressInterface;
    }

    private function applyCustomer(OrderInterface $order, BillingAddressOrder $addressOrder): void
    {
        if (null === $order->getCustomer() && null !== $addressOrder->email) {
            $order->setCustomer($this->customerProvider->provide($addressOrder->email));
        }
    }

    private function applyBillingAddress(BillingAddressOrder $addressOrder, OrderInterface $order): void
    {
        if ($addressOrder->billingAddress !== null && $addressOrder->billingAddress instanceof AddressInterface) {
            /** @psalm-suppress ArgumentTypeCoercion */
            $order->setBillingAddress($addressOrder->billingAddress);
        }
    }
}
