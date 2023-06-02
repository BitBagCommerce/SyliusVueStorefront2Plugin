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
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusVueStorefront2Plugin\Behat\Client\GraphqlClientInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    private RepositoryInterface $shopUserRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $shopUserRepository,
        WishlistRepositoryInterface $wishlistRepository,
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->shopUserRepository = $shopUserRepository;
        $this->wishlistRepository = $wishlistRepository;
    }

    /**
     * @Given I prepare login operation with username :email and password :password
     */
    public function iPrepareLoginOperation(string $email, string $password): void
    {
        $expectedData = '
        shopUserToken{
            user {
                username
            }
            token
            refreshToken
        }';

        $operation = $this->client->prepareOperation('shop_loginShopUserToken', $expectedData);
        $operation->setVariables([
            'username' => $email,
            'password' => $password,
            'rememberMe' => true,
        ]);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Then The user :userEmail has default wishlist :wishlistName
     */
    public function theUserHasDefaultWishlist(
        string $userEmail,
        string $wishlistName
    ): void {
        $shopUser = $this->shopUserRepository->findOneBy([
            'username' => $userEmail,
        ]);
        Assert::notNull($shopUser);

        $channel = $this->sharedStorage->get('channel');
        Assert::notNull($channel);

        $wishlists = $this->wishlistRepository->findBy([
            'shopUser' => $shopUser,
            'channel' => $channel,
        ]);
        Assert::count($wishlists, 1);

        /** @var WishlistInterface $wishlist */
        $wishlist = $wishlists[0];
        Assert::eq($wishlist->getName(), $wishlistName);
    }
}
