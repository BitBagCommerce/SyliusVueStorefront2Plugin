<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\CommandHandler\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\BillingAddressOrder;
use BitBag\SyliusGraphqlPlugin\Command\Checkout\BillingAddressOrderSpec;
use BitBag\SyliusGraphqlPlugin\CommandHandler\Checkout\BillingAddressOrderHandler;
use BitBag\SyliusGraphqlPlugin\Resolver\OrderAddressStateResolverInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Webmozart\Assert\InvalidArgumentException;


final class BillingAddressOrderHandlerSpec extends ObjectBehavior
{

    function let(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $manager,
        CustomerProviderInterface $customerProvider,
        OrderAddressStateResolverInterface $addressStateResolver
    ): void
    {
        $this->beConstructedWith($orderRepository, $manager, $customerProvider, $addressStateResolver);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(BillingAddressOrderHandler::class);
    }

    function it_is_invokable(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $manager,
        CustomerProviderInterface $customerProvider,
        OrderAddressStateResolverInterface $addressStateResolver,
        OrderInterface $order,
        CustomerInterface $customer
    ): void
    {
        $addressOrder = new BillingAddressOrder("jd@mail.com", "token");
        $tokenValue = $addressOrder->orderTokenValue;

        $orderRepository->findCartByTokenValue($tokenValue)->willReturn($order);

        $order->getCustomer()->willReturn($customer);
        $order->setBillingAddress($addressOrder->billingAddress);

        $addressStateResolver->resolve($order)->shouldBeCalled();
        $manager->persist($order)->shouldBeCalled();

        $this->__invoke($addressOrder);
    }

    function it_throws_exception_when_cannot_find_cart(
        OrderRepositoryInterface $orderRepository
    ): void
    {
        $addressOrder = new BillingAddressOrder("jd@mail.com", "token");
        $tokenValue = $addressOrder->orderTokenValue;

        $orderRepository->findCartByTokenValue($tokenValue)->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addressOrder]);
    }
}
