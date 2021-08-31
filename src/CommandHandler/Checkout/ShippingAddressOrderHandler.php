<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\ShippingAddressOrder;
use BitBag\SyliusGraphqlPlugin\Resolver\OrderAddressStateResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ShippingAddressOrderHandler implements MessageHandlerInterface
{
    private OrderRepositoryInterface $orderRepository;

    private ObjectManager $manager;

    private CustomerProviderInterface $customerProvider;

    private OrderAddressStateResolverInterface $addressStateResolver;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $manager,
        CustomerProviderInterface $customerProvider,
        OrderAddressStateResolverInterface $addressStateResolver
    )
    {
        $this->orderRepository = $orderRepository;
        $this->manager = $manager;
        $this->customerProvider = $customerProvider;
        $this->addressStateResolver = $addressStateResolver;
    }

    public function __invoke(ShippingAddressOrder $addressOrder): OrderInterface
    {
        $tokenValue = $addressOrder->orderTokenValue;

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findCartByTokenValue($tokenValue);
        Assert::notNull($order, sprintf('Order with %s token has not been found.', $tokenValue));

        if (null === $order->getCustomer() || null !== $addressOrder->email) {
            $order->setCustomer($this->customerProvider->provide($addressOrder->email));
        }

        if ($addressOrder->shippingAddress !== null) {
            $order->setShippingAddress($addressOrder->shippingAddress);
        }

        $this->addressStateResolver->resolve($order);

        $this->manager->persist($order);

        return $order;
    }
}
