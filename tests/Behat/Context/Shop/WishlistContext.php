<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Context\Shop;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClientInterface;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Model\OperationRequestInterface;

final class WishlistContext implements Context
{
    private GraphqlClientInterface $client;
    private SharedStorageInterface $sharedStorage;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage,
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I prepare query to fetch all wishlists
     */
    public function iPrepareQueryToFetchAllWishlists(): void
    {
        $expectedData = '
        collection {
            id,
            name,
            wishlistProducts {
                edges {
                    node {
                        id,
                        variant {
                            name
                        },
                    },
                },
            },
        }';

        $operation = $this->client->prepareQuery('wishlists', $expectedData);
        $operation->setOperationType(OperationRequestInterface::OPERATION_QUERY);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare create wishlist operation
     */
    public function iPrepareCreateWishlistOperation(): void
    {
        $expectedData = '
        wishlist {
            id,
            name,
            wishlistProducts {
                totalCount,
            },
        }';

        $operation = $this->client->prepareOperation('createWishlist', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare update wishlist operation
     */
    public function iPrepareUpdateWishlistOperation(): void
    {
        $expectedData = '
        wishlist {
            id,
            name,
        }';

        $operation = $this->client->prepareOperation('updateWishlist', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare delete wishlist operation
     */
    public function iPrepareDeleteWishlistOperation(): void
    {
        $expectedData = '
        wishlist {
            id,
        }';

        $operation = $this->client->prepareOperation('deleteWishlist', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare add product to wishlist operation
     */
    public function iPrepareAddProductToWishlistOperation(): void
    {
        $expectedData = '
        wishlist {
            id,
            name,
            wishlistProducts {
                totalCount,
                edges {
                    node {
                        id,
                        variant {
                            name
                        },
                    },
                },
            },
        }';

        $operation = $this->client->prepareOperation('add_itemWishlist', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare remove product from wishlist operation
     */
    public function iPrepareRemoveProductFromWishlistOperation(): void
    {

        $expectedData = '
        wishlist {
            id,
            name,
            wishlistProducts {
                totalCount,
                edges {
                    node {
                        id,
                        variant {
                            name
                        },
                    },
                },
            },
        }';

        $operation = $this->client->prepareOperation('remove_itemWishlist', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }
}
