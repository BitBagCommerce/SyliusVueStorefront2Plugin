<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Resolver\Query;

use ApiPlatform\GraphQl\Resolver\QueryItemResolverInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

class PasswordResetTokenResolver implements QueryItemResolverInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke($item, array $context): ?ShopUserInterface
    {
        $token = $context['args']['passwordResetToken'];
        /** @var ?ShopUserInterface $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $token]);

        if (null !== $user) {
            return $user;
        }

        return null;
    }
}
