<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Model;

use Sylius\Component\Core\Model\ShopUserInterface;

final class ShopUserToken implements ShopUserTokenInterface
{
    private int $id;

    private ?string $token;

    private ?string $refreshToken;

    private ?ShopUserInterface $user;

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): ?ShopUserInterface
    {
        return $this->user;
    }

    public function setUser(?ShopUserInterface $user): void
    {
        $this->user = $user;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }
}
