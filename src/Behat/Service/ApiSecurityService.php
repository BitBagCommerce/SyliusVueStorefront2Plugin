<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Behat\Service;

use BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Webmozart\Assert\Assert;

final class ApiSecurityService implements SecurityServiceInterface
{
    /** @var GraphqlClientInterface */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(GraphqlClientInterface $client, SharedStorageInterface $sharedStorage)
    {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    public function logIn(UserInterface $user): void
    {
        //TODO::
        $token = '';
        $this->sharedStorage->set('token', $token);
    }

    public function logOut(): void
    {
        $this->sharedStorage->set('token', null);
    }

    public function getCurrentToken(): TokenInterface
    {
        $token = new JWTUserToken();
        $storageToken = $this->sharedStorage->get('token');
        Assert::string($storageToken);
        $token->setRawToken($storageToken);

        return $token;
    }

    public function restoreToken(TokenInterface $token): void
    {
        $this->sharedStorage->set('token', (string) $token);
    }
}
