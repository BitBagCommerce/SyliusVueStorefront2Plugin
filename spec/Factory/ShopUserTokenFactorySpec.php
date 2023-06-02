<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Factory;

use BitBag\SyliusVueStorefront2Plugin\Factory\ShopUserTokenFactory;
use BitBag\SyliusVueStorefront2Plugin\Model\ShopUserToken;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;

final class ShopUserTokenFactorySpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshJwtManager,
    ): void {
        $this->beConstructedWith($entityManager, $jwtManager, $refreshJwtManager, '+5 second', '+3 month');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShopUserTokenFactory::class);
    }

    public function it_creates_shop_user_token(
        JWTTokenManagerInterface $jwtManager,
        ShopUserInterface $user,
        RefreshTokenInterface $refreshToken,
    ): void {
        $shopUserToken = new ShopUserToken();
        $token = 'token';
        $jwtManager->create($user)->willReturn($token);
        $userId = 1;
        $user->getId()->willReturn($userId);
        $shopUserToken->setId($userId);
        $shopUserToken->setToken($token);

        $refreshTokenString = 'refreshToken';
        $refreshToken->getRefreshToken()->willReturn($refreshTokenString);
        $shopUserToken->setRefreshToken($refreshTokenString);

        $shopUserToken->setUser($user->getWrappedObject());

        $this->create($user, $refreshToken)->shouldBeLike($shopUserToken);
    }

    public function it_gets_refresh_token(
        EntityManagerInterface $entityManager,
        RefreshTokenManagerInterface $refreshJwtManager,
        RefreshTokenInterface $refreshToken,
        ShopUserInterface $user,
    ): void {
        $refreshJwtManager->create()->willReturn($refreshToken);

        $username = 'canonical';
        $user->getUsernameCanonical()->willReturn($username);
        $refreshToken->setUsername($username)->shouldBeCalled();

        $refreshToken->setRefreshToken()->shouldBeCalled();

        $refreshToken->setValid(Argument::type(\DateTime::class))->shouldBeCalled();

        $entityManager->persist($refreshToken)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->getRefreshToken($user)->shouldReturn($refreshToken);
    }
}
