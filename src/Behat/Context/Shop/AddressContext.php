<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Behat\Context\Shop;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClient;
use BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

final class AddressContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    private AddressRepositoryInterface $addressRepository;

    private UserRepositoryInterface $userRepository;

    private IriConverterInterface $iriConverter;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage,
        AddressRepositoryInterface $addressRepository,
        UserRepositoryInterface $userRepository,
        IriConverterInterface $iriConverter,
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->addressRepository = $addressRepository;
        $this->userRepository = $userRepository;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @Given I prepare add address operation
     */
    public function iPrepareAddAddressOperation(): void
    {
        $expectedData = '
        address{
            id
            _id
            firstName
            lastName
            phoneNumber
            company
            countryCode
            provinceName
            street
            city
            postcode
        }';

        $operation = $this->client->prepareOperation('shop_postAddress', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given I prepare edit address operation
     */
    public function iPrepareEditAddressOperation(): void
    {
        $expectedData = '
        address{
            id
            _id
            firstName
            lastName
            phoneNumber
            company
            countryCode
            provinceName
            street
            city
            postcode
        }';

        $operation = $this->client->prepareOperation('shop_putAddress', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given I prepare operation to fetch collection of user addresses
     * @Given I prepare get address collection operation
     */
    public function iPrepareUserAddressesCollectionQueryOperation(): void
    {
        $expectedData = '
        collection{
            id
            firstName
            lastName
            phoneNumber
            company
            countryCode
            provinceName
            street
            city
            postcode
        }';

        $operation = $this->client->prepareQuery('addresses', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare delete address operation
     */
    public function iPrepareDeleteAddressOperation(): void
    {
        $expectedData = 'clientMutationId';

        $operation = $this->client->prepareOperation('deleteAddress', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given customer identified by an email :email has an address
     */
    public function customerHasAnAddress(string $email): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['username' => $email]);

        Assert::notNull($user);

        /** @var Customer $customer */
        $customer = $user->getCustomer();
        $address = new Address();

        $address->setFirstName($customer->getFirstName());
        $address->setLastName($customer->getLastName());
        $address->setStreet('Street');
        $address->setCity('City');
        $address->setPostcode('00000');
        $address->setCountryCode('US');
        $address->setCustomer($customer);

        $this->addressRepository->add($address);
        $iri = $this->iriConverter->getIriFromItem($address);
        $this->sharedStorage->set('customerAddressIri', $iri);
    }
}
