<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Behat\Context\Shop;

use Behat\Behat\Context\Context;
use BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClient;
use BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;
use Sylius\Behat\Service\SharedStorageInterface;

final class TaxonContext implements Context
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
     * @When I prepare query to fetch all taxa
     */
    public function iPrepareFetchTaxaQuery(): void
    {
        $expectedData = '
        collection {
            root{
                id
                slug
            }
            id
            _id
            name
            fullname
            slug
            code
            enabled
            position
            description
            left
            right
            level
            createdAt
            updatedAt
            translations{
            collection{
                slug
                name
                locale
            }
        }';

        $operation = $this->client->prepareQuery('taxa', $expectedData);
        $operation->setOperationType(OperationRequestInterface::OPERATION_QUERY);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }
}
