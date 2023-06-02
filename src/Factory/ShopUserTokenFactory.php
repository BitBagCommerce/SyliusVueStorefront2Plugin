<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Factory;

use BitBag\SyliusVueStorefront2Plugin\Model\ShopUserToken;
use BitBag\SyliusVueStorefront2Plugin\Model\ShopUserTokenInterface;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

class ShopUserTokenFactory implements ShopUserTokenFactoryInterface
{
    private JWTTokenManagerInterface $jwtManager;

    private RefreshTokenManagerInterface $refreshJwtManager;

    private EntityManagerInterface $entityManager;

    private string $refreshTokenTTL;

    private string $refreshTokenExtendedTTL;

    public function __construct(
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshJwtManager,
        string $refreshTokenTTL,
        string $refreshTokenExtendedTTL,
    ) {
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->refreshJwtManager = $refreshJwtManager;
        $this->refreshTokenTTL = $refreshTokenTTL;
        $this->refreshTokenExtendedTTL = $refreshTokenExtendedTTL;
    }

    public function create(
        ShopUserInterface $user,
        RefreshTokenInterface $refreshToken,
    ): ShopUserTokenInterface {
        $shopUserToken = new ShopUserToken();
        $token = $this->jwtManager->create($user);
        $shopUserToken->setId((int) $user->getId());
        $shopUserToken->setToken($token);
        $shopUserToken->setRefreshToken($refreshToken->getRefreshToken());
        $shopUserToken->setUser($user);

        return $shopUserToken;
    }

    public function getRefreshToken(
        ShopUserInterface $user,
        ?bool $rememberMe = null
    ): RefreshTokenInterface {

        $refreshTokenExpirationDate = new \DateTime(null !== $rememberMe ? $this->refreshTokenExtendedTTL : $this->refreshTokenTTL);
        $refreshToken = $this->refreshJwtManager->create();
        $refreshToken->setRefreshToken();
        $refreshToken->setUsername((string) $user->getUsernameCanonical());
        $refreshToken->setValid($refreshTokenExpirationDate);

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $refreshToken;
    }
}
