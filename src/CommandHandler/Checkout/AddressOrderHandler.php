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

use BitBag\SyliusGraphqlPlugin\Command\Checkout\AddressOrder;
use Doctrine\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddressOrderHandler implements MessageHandlerInterface
{
    private OrderRepositoryInterface $orderRepository;

    private CustomerRepositoryInterface $customerRepository;

    private FactoryInterface $customerFactory;

    private ObjectManager $manager;

    private StateMachineFactoryInterface $stateMachineFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory
    )
    {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->manager = $manager;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function __invoke(AddressOrder $addressOrder): OrderInterface
    {
        $tokenValue = $addressOrder->orderTokenValue;

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $tokenValue]);
        Assert::notNull($order, sprintf('Order with %s token has not been found.', $tokenValue));

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS),
            sprintf('Order with %s token cannot be addressed.', $tokenValue)
        );

        if (null === $order->getCustomer()) {
            $order->setCustomer($this->provideCustomerByEmail($addressOrder->email));
        }

        $order->setBillingAddress($addressOrder->billingAddress);
        $order->setShippingAddress($addressOrder->shippingAddress ?? clone $addressOrder->billingAddress);

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->manager->persist($order);

        return $order;
    }

    private function provideCustomerByEmail(?string $email): CustomerInterface
    {
        Assert::notNull($email, sprintf('Visitor should provide an email.'));

        $customer = $this->customerRepository->findOneBy(['email' => $email]);
        if (null === $customer) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);
            $this->manager->persist($customer);
        }

        return $customer;
    }
}
