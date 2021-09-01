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
}
