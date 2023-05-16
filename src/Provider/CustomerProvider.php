<?php

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Provider;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

/** @experimental */
final class CustomerProvider implements CustomerProviderInterface
{
    public function __construct(
        private CanonicalizerInterface $canonicalizer,
        private FactoryInterface $customerFactory,
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function provide(string $email): CustomerInterface
    {
        $emailCanonical = $this->canonicalizer->canonicalize($email);

        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['emailCanonical' => $emailCanonical]);

        if ($customer === null) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);
        }

        return $customer;
    }
}
