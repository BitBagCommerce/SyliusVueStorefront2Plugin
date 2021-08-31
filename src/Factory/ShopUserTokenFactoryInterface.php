<?php


namespace BitBag\SyliusGraphqlPlugin\Factory;


use BitBag\SyliusGraphqlPlugin\Model\ShopUserTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

interface ShopUserTokenFactoryInterface
{
    public function create(ShopUserInterface $user, RefreshTokenInterface $refreshToken): ShopUserTokenInterface;
    public function getRefreshToken(ShopUserInterface $user): RefreshTokenInterface;
}
