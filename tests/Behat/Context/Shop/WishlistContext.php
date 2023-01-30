<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Context\Shop;

use Behat\Behat\Context\Context;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClientInterface;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Model\OperationRequestInterface;
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private WishlistRepositoryInterface $wishlistRepository;

    private UserRepositoryInterface $userRepository;

    private ProductRepositoryInterface $productRepository;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage,
        WishlistFactoryInterface $wishlistFactory,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistRepositoryInterface $wishlistRepository,
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository,
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistRepository = $wishlistRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @When I prepare query to fetch all wishlists
     */
    public function iPrepareQueryToFetchAllWishlists(): void
    {
        $expectedData = '
        paginationInfo {
            totalCount,
        },
        collection {
            id,
            name,
            wishlistProducts {
                totalCount,
                edges {
                    node {
                        id,
                        variant {
                            name,
                            id,
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
     * @When I prepare remove wishlist operation
     */
    public function iPrepareRemoveWishlistOperation(): void
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

    /**
     * @Given anonymous user has a wishlist named :name
     */
    public function anonymousUserHasAWishlistNamed(string $name): void
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();
        $wishlist->setName($name);

        $this->saveWishlist($wishlist, $name);
    }

    /**
     * @Given user :email has a wishlist named :name
     */
    public function userHasAWishlistNamed(string $name, string $email): void
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistFactory->createNew();
        $wishlist->setName($name);

        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['username' => $email]);
        Assert::notNull($user);

        $wishlist->setShopUser($user);

        $this->saveWishlist($wishlist, $name);
    }

    /**
     * @Then I should receive :count wishlists
     */
    public function iShouldReceiveWishlists(int $count): void
    {
        $total = (int) $this->client->getValueAtKey('paginationInfo.totalCount');
        Assert::same($total, $count);
    }

    public function saveWishlist(WishlistInterface $wishlist, string $name): void
    {
        $this->wishlistRepository->add($wishlist);
        $this->sharedStorage->set($name, $wishlist);
    }

    /**
     * @Given user have a product :code in my wishlist :name
     */
    public function userHaveAProductInMyWishlist(string $code, string $name): void
    {
        $product = $this->productRepository->findOneByCode($code);
        Assert::notNull($product);

        /** @var WishlistInterface|null $wishlist */
        $wishlist = $this->sharedStorage->get($name);
        Assert::notNull($wishlist);

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);
        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistRepository->add($wishlist);
    }
}
