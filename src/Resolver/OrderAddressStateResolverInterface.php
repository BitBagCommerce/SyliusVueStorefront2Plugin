<?php


namespace BitBag\SyliusGraphqlPlugin\Resolver;


use Sylius\Component\Core\Model\OrderInterface;

interface OrderAddressStateResolverInterface
{
    public function resolve(OrderInterface $order): void;
}
