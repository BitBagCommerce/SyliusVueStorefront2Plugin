<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Context\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;

final class RegistrationContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I want to register new user by GraphQl
     * @When I prepare register user operation
     */
    public function iPrepareRegisterUserOperation(): void
    {
        $expectedData = '
        user {
            username
            customer{
                id
                email
            }
        }';

        $operation = $this->client->prepareOperation('shop_registerUser', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Then I provide first name :arg
     */
    public function iProvideFirstName(string $argument): void
    {
        /** @var OperationRequestInterface $operation */
        $operation = $this->client->getLastOperationRequest();
        $operation->addVariable('firstName', $argument);
    }

    /**
     * @Then I provide last name :arg
     */
    public function iProvideLastName(string $argument): void
    {
        /** @var OperationRequestInterface $operation */
        $operation = $this->client->getLastOperationRequest();
        $operation->addVariable('lastName', $argument);
    }

    /**
     * @Then I provide email :arg
     */
    public function iProvideEmail(string $argument): void
    {
        /** @var OperationRequestInterface $operation */
        $operation = $this->client->getLastOperationRequest();
        $operation->addVariable('email', $argument);
    }

    /**
     * @Then I provide password :arg
     */
    public function iProvidePassword(string $argument): void
    {
        /** @var OperationRequestInterface $operation */
        $operation = $this->client->getLastOperationRequest();
        $operation->addVariable('password', $argument);
    }

    /**
     * @Then I provide phone number :arg
     */
    public function iProvidePhoneNumber(string $argument): void
    {
        /** @var OperationRequestInterface $operation */
        $operation = $this->client->getLastOperationRequest();
        $operation->addVariable('phoneNumber', $argument);
    }

    /**
     * @Then I want to be subscribed to newsletter
     */
    public function iWantToBeNewsletterSubscriber(): void
    {
        /** @var OperationRequestInterface $operation */
        $operation = $this->client->getLastOperationRequest();
        $operation->addVariable('subscribedToNewsletter', true);
    }

    /**
     * @Then I dont want to be subscribed to newsletter
     */
    public function iDontWantToBeNewsletterSubscriber(): void
    {
        /** @var OperationRequestInterface $operation */
        $operation = $this->client->getLastOperationRequest();
        $operation->addVariable('subscribedToNewsletter', false);
    }
}
