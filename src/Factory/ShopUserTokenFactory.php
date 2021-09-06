<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Factory;

use BitBag\SyliusGraphqlPlugin\Model\ShopUserToken;
use BitBag\SyliusGraphqlPlugin\Model\ShopUserTokenInterface;
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

    public function __construct(
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshJwtManager
    ) {
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->refreshJwtManager = $refreshJwtManager;
    }

    public function create(
        ShopUserInterface $user,
        RefreshTokenInterface $refreshToken
    ): ShopUserTokenInterface {
        $shopUserToken = new ShopUserToken();
        $token = $this->jwtManager->create($user);
        $shopUserToken->setId((int) $user->getId());
        $shopUserToken->setToken($token);
        $shopUserToken->setRefreshToken($refreshToken->getRefreshToken());
        $shopUserToken->setUser($user);

        return $shopUserToken;
    }

    public function getRefreshToken(ShopUserInterface $user): RefreshTokenInterface
    {
        $refreshTokenExpirationDate = new \DateTime('+1 month');
        $payload = [
            'exp' => $refreshTokenExpirationDate->getTimestamp(),
        ];
        $refreshTokenString = $this->jwtManager->createFromPayload($user, $payload);
        $refreshToken = $this->refreshJwtManager->create();
        $refreshToken->setRefreshToken($refreshTokenString);
        $refreshToken->setUsername((string) $user->getUsernameCanonical());
        $refreshToken->setValid($refreshTokenExpirationDate);

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $refreshToken;
    }
}
