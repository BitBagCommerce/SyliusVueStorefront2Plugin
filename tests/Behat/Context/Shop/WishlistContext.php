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
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClientInterface;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Model\OperationRequestInterface;
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    private IriConverterInterface $iriConverter;

    private WishlistFactoryInterface $wishlistFactory;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private WishlistRepositoryInterface $wishlistRepository;

    private UserRepositoryInterface $userRepository;

    private ProductRepositoryInterface $productRepository;

    private ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage,
        IriConverterInterface $iriConverter,
        WishlistFactoryInterface $wishlistFactory,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistRepositoryInterface $wishlistRepository,
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->iriConverter = $iriConverter;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistRepository = $wishlistRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * @Given There is query to fetch all wishlists
     */
    public function thereIsQueryToFetchAllWishlists(): void
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
     * @Given There is operation to create wishlist
     */
    public function thereIsOperationToCreateWishlist(): void
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
     * @Given There is operation to update wishlist
     */
    public function thereIsOperationToUpdateWishlist(): void
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
     * @Given There is operation to clear wishlist
     */
    public function thereIsOperationToClearWishlist(): void
    {
        $expectedData = '
        wishlist {
            id,
            name,
            wishlistProducts {
                totalCount,
            },
        }';

        $operation = $this->client->prepareOperation('clearWishlist', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given There is operation to remove wishlist
     */
    public function thereIsOperationToRemoveWishlist(): void
    {
        $expectedData = '
        wishlist {
            id,
        }';

        $operation = $this->client->prepareOperation('deleteWishlist', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Given There is operation to add product to wishlist
     */
    public function thereIsOperationToAddProductToWishlist(): void
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
     * @Given There is operation to remove product from wishlist
     */
    public function thereIsOperationToRemoveProductFromWishlist(): void
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

    /**
     * @Then user :email should have :count wishlists
     */
    public function userHasWishlists(string $email, int $count): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['username' => $email]);
        Assert::notNull($user);

        $wishlists = $this->wishlistRepository->findAllByShopUser($user->getId());
        Assert::count($wishlists, $count);
    }

    /**
     * @Given this operation has :key variable with iri product variant :code
     */
    public function thisOperationHasVariableWithIriProductVariant(string $key, string $code): void
    {
        $operation = $this->client->getLastOperationRequest();
        Assert::isInstanceOf($operation, OperationRequestInterface::class);

        $productVariant = $this->productVariantRepository->findOneBy(['code' => $code]);
        Assert::notNull($productVariant);
        $iri = $this->iriConverter->getIriFromItem($productVariant);
        $operation->addVariable($key, $iri);
    }
}
