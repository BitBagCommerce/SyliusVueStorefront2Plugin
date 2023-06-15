<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);


namespace spec\BitBag\SyliusVueStorefront2Plugin\Resolver\Query;

use BitBag\SyliusVueStorefront2Plugin\Resolver\Query\PasswordResetTokenResolver;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

class PasswordResetTokenResolverSpec extends ObjectBehavior
{
    public function let(
        UserRepositoryInterface $userRepository
    ): void {
        $this->beConstructedWith(
            $userRepository
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PasswordResetTokenResolver::class);
    }

    public function it_returns_user_on_valid_token(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $user
    ): void {
        $context = [
            'args' => [
                'passwordResetToken' => "TOKEN",
            ],
        ];

        $token = $context['args']['passwordResetToken'];

        $userRepository->findOneBy(['passwordResetToken' => $token])->willReturn($user);

        $this->__invoke(null, $context)->shouldReturn($user);
    }

    public function it_should_return_null_on_invalid_token(
        UserRepositoryInterface $userRepository,
    ): void {
        $context = [
            'args' => [
                'passwordResetToken' => "TOKEN",
            ],
        ];

        $token = $context['args']['passwordResetToken'];

        $userRepository->findOneBy(['passwordResetToken' => $token])->willReturn(null);

        $this->__invoke(null, $context)->shouldReturn(null);
    }
}
