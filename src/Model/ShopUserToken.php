<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
