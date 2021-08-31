<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Model;


use Sylius\Component\Core\Model\ShopUserInterface;

interface ShopUserTokenInterface
{

    public function getToken(): ?string;

    public function setToken(?string $token): void;

    public function setId(int $id): void;

    public function getId(): int;

    public function getUser(): ?ShopUserInterface;

    public function setUser(?ShopUserInterface $user): void;

    public function getRefreshToken(): ?string;

    public function setRefreshToken(?string $refreshToken): void;
}
