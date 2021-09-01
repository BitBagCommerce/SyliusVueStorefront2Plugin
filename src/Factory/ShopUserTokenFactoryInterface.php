<?php

declare(strict_types=1);

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

namespace BitBag\SyliusGraphqlPlugin\Factory;

use BitBag\SyliusGraphqlPlugin\Model\ShopUserTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

interface ShopUserTokenFactoryInterface
{
    public function create(ShopUserInterface $user, RefreshTokenInterface $refreshToken): ShopUserTokenInterface;

    public function getRefreshToken(ShopUserInterface $user): RefreshTokenInterface;
}
