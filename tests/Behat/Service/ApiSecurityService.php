<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;

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
        $token = "";
        $this->sharedStorage->set('token', $token);
    }

    public function logOut(): void
    {
        $this->sharedStorage->set('token', null);
    }

    public function getCurrentToken(): TokenInterface
    {
        $token = new JWTUserToken();
        $token->setRawToken($this->sharedStorage->get('token'));

        return $token;
    }

    public function restoreToken(TokenInterface $token): void
    {
        $this->sharedStorage->set('token', (string)$token);
    }
}