<?php


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
