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
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;

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

    //TODO::

    /**
     * @When I want to register new user by GraphQl
     */
    public function iWantToRegisterNewUserByGraphQl(){

    }

    /**
     * @Then I provide his first name : $arg
     */
    public function iProvideHisFirstName(){

    }

    /**
     * @Then I provide his last name : $arg
     */
    public function iProvideHisLastName(){

    }

    /**
     * @Then I provide his email : $arg
     */
    public function iProvideHisEmail(){

    }

    /**
     * @Then I provide his password : $arg
     */
    public function iProvideHisPassword(){

    }

    /**
     * @Then I want to be subscribed to newsletter
     */
    public function iWantToBeNewsletterSubscriber(){

    }

}
