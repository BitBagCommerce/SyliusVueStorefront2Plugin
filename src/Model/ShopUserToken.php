<?php

//*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Model;

use Sylius\Component\Core\Model\ShopUserInterface;

final class ShopUserToken
{
    private int $id;

    protected ?string $token;

    public ?ShopUserInterface $user;

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

}
