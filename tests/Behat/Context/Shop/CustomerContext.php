<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Context\Shop;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use BitBag\SyliusVueStorefront2Plugin\Factory\ShopUserTokenFactoryInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClientInterface;
use Webmozart\Assert\Assert;

final class CustomerContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    private UserRepositoryInterface $userRepository;

    private ShopUserTokenFactoryInterface $tokenFactory;

    private IriConverterInterface $iriConverter;

    private EntityManagerInterface $entityManager;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage,
        UserRepositoryInterface $userRepository,
        ShopUserTokenFactoryInterface $tokenFactory,
        IriConverterInterface $iriConverter,
        EntityManagerInterface $entityManager,
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->userRepository = $userRepository;
        $this->tokenFactory = $tokenFactory;
        $this->iriConverter = $iriConverter;
        $this->entityManager = $entityManager;
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase()
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
        $this->entityManager->clear();
    }

    /**
     * @When I prepare edit customer account operation
     */
    public function iPrepareEditAccountOperation(): void
    {
        $expectedData = '
        customer{
            user {
                username
            }
            id
            firstName
            lastName
            birthday
            subscribedToNewsletter
            phoneNumber
        }';

        $operation = $this->client->prepareOperation('shop_putCustomer', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare change customer password operation
     */
    public function iPrepareChangeCustomerPasswordOperation(): void
    {
        $expectedData = '
        customer {
            user{
                id
                username
            }
        }';

        $operation = $this->client->prepareOperation('shop_password_updateCustomer', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given I authorize as :email
     * @When I create a JWT Token for customer identified by an email :email
     */
    public function iCreateJWTTokenForCustomer(string $email): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['username' => $email]);
        Assert::notNull($user);
        $refreshToken = $this->tokenFactory->getRefreshToken($user);
        $shopUserToken = $this->tokenFactory->create($user, $refreshToken);

        $this->sharedStorage->set('token', $shopUserToken->getToken());
        $this->sharedStorage->set('refreshToken', $refreshToken->getRefreshToken());
    }

    /**
     * @When I set IRI of this customer request to match :arg
     */
    public function iSetIriForThisCustomerRequest(string $email): void
    {
        $operation = $this->client->getLastOperationRequest();
        Assert::notNull($operation);

        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['username' => $email]);
        Assert::notNull($user);
        $customer = $user->getCustomer();
        Assert::notNull($customer);

        $id = $this->iriConverter->getIriFromItem($customer);
        $operation->addVariable('id', $id);
    }

    /**
     * @When I set id of this user request to match :arg
     */
    public function iSetIdForThisUserRequest(string $email): void
    {
        $operation = $this->client->getLastOperationRequest();
        Assert::notNull($operation);

        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['username' => $email]);
        Assert::notNull($user);

        $operation->addVariable('shopUserId', (string) $user->getId());
    }

    /**
     * @When I prepare refresh JWT Token operation
     */
    public function iPrepareRefreshJwtTokenOperation(): void
    {
        $expectedData = '
        shopUserToken {
            token
            refreshToken
            user{
                id
                username
                customer{
                    id
                    firstName
                }
            }
        }';

        $operation = $this->client->prepareOperation('shop_refreshShopUserToken', $expectedData);
        $refreshToken = (string) $this->sharedStorage->get('refreshToken');
        $operation->addVariable('refreshToken', $refreshToken);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given I prepare password reset request operation for user :email
     */
    public function iPreparePasswordResetRequestOperation(string $email): void
    {
        $mutationBody = '
        customer {
            id
        }';

        /** @var LocaleInterface $locale */
        $locale = $this->sharedStorage->get('locale');

        $operation = $this->client->prepareOperation('shop_send_reset_password_emailCustomer', $mutationBody);
        $operation->addVariable('email', $email);
        $operation->addVariable('localeCode', $locale->getCode());
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given /^user "([^"]*)" should have reset password token set$/
     */
    public function userShouldHaveResetPasswordTokenSet(string $email)
    {
        $user = $this->userRepository->findOneByEmail($email);
        Assert::notNull($user->getPasswordResetToken());
    }

    /**
     * @Given /^I prepare check reset password token operation for user's "([^"]*)" token$/
     */
    public function iPrepareCheckResetPasswordTokenOperationForUserSToken(string $email)
    {
        $user = $this->userRepository->findOneByEmail($email);
        $token = "\"" . $user->getPasswordResetToken() . "\"";
        $queryBody = 'username';
        $operation = $this->client->prepareQuery('password_reset_tokenUser', $queryBody);

        $operation->addFilter('passwordResetToken', $token);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given /^I prepare check reset password token operation for invalid token$/
     */
    public function iPrepareCheckResetPasswordTokenOperationForUserSInvalidToken()
    {
        $token = "\"invalid-token\"";
        $queryBody = 'username';
        $operation = $this->client->prepareQuery('password_reset_tokenUser', $queryBody);

        $operation->addFilter('passwordResetToken', $token);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }
}
