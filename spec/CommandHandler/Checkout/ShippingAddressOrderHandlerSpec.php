<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\CommandHandler\Checkout;

use BitBag\SyliusVueStorefront2Plugin\Command\Checkout\ShippingAddressOrder;
use BitBag\SyliusVueStorefront2Plugin\CommandHandler\Checkout\ShippingAddressOrderHandler;
use BitBag\SyliusVueStorefront2Plugin\Resolver\OrderAddressStateResolverInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use BitBag\SyliusVueStorefront2Plugin\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\InvalidArgumentException;

final class ShippingAddressOrderHandlerSpec extends ObjectBehavior
{
    public function let(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $manager,
        CustomerProviderInterface $customerProvider,
        OrderAddressStateResolverInterface $addressStateResolver,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $this->beConstructedWith($orderRepository, $manager, $customerProvider, $addressStateResolver, $eventDispatcher);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingAddressOrderHandler::class);
    }

    public function it_is_invokable(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $manager,
        OrderAddressStateResolverInterface $addressStateResolver,
        OrderInterface $order,
        CustomerInterface $customer,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $addressOrder = new ShippingAddressOrder('jd@mail.com', 'token');
        $tokenValue = $addressOrder->orderTokenValue;

        $orderRepository->findCartByTokenValue($tokenValue)->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $order->setShippingAddress($addressOrder->shippingAddress)->shouldNotBeCalled();
        $addressStateResolver->resolve($order)->shouldBeCalled();
        $manager->persist($order)->shouldBeCalled();

        $eventDispatcher->dispatch(Argument::any(), ShippingAddressOrderHandler::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($addressOrder);
    }

    public function it_throws_an_exception_when_cannot_find_cart(
        OrderRepositoryInterface $orderRepository,
    ): void {
        $addressOrder = new ShippingAddressOrder('jd@mail.com', 'token');
        $tokenValue = $addressOrder->orderTokenValue;

        $orderRepository->findCartByTokenValue($tokenValue)->willReturn(null);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('__invoke', [$addressOrder])
        ;
    }
}
