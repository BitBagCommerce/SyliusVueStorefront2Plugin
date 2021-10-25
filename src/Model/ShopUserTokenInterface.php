<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Model;

use Sylius\Component\Core\Model\ShopUserInterface;

interface ShopUserTokenInterface
{
    public function getToken(): string;

    public function setToken(string $token): void;

    public function setId(int $id): void;

    public function getId(): int;

    public function getUser(): ?ShopUserInterface;

    public function setUser(?ShopUserInterface $user): void;

    public function getRefreshToken(): string;

    public function setRefreshToken(string $refreshToken): void;
}
