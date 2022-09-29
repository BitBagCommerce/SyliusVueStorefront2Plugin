<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\Resolver\Mutation;

use BitBag\SyliusGraphqlPlugin\Factory\ShopUserTokenFactoryInterface;
use BitBag\SyliusGraphqlPlugin\Model\ShopUserTokenInterface;
use BitBag\SyliusGraphqlPlugin\Resolver\Mutation\LoginResolver;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class LoginResolverSpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $entityManager,
        UserRepositoryInterface $userRepository,
        OrderRepositoryInterface $orderRepository,
        EncoderFactoryInterface $encoderFactory,
        ShopUserTokenFactoryInterface $tokenFactory,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $this->beConstructedWith(
            $entityManager,
            $userRepository,
            $orderRepository,
            $encoderFactory,
            $tokenFactory,
            $eventDispatcher
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(LoginResolver::class);
    }

    function it_is_invokable(
        UserRepositoryInterface $userRepository,
        EncoderFactoryInterface $encoderFactory,
        ShopUserTokenFactoryInterface $tokenFactory,
        PasswordEncoderInterface $encoder,
        ShopUserInterface $user,
        RefreshTokenInterface $refreshToken,
        ShopUserTokenInterface $shopUserToken,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $context = [
            'args' => [
                'input' => [
                    'username' => 'username',
                    'password' => 'somepass',
                ],
            ],
        ];

        $input = $context['args']['input'];
        $username = (string) $input['username'];
        $password = (string) $input['password'];

        $userRepository->findOneBy(['username' => $username])->willReturn($user);
        $encoderFactory->getEncoder($user)->willReturn($encoder);

        $userPassword = 'ENCODED_PASSWORD';
        $userSalt = 'SALT';
        $user->getPassword()->willReturn($userPassword);
        $user->getSalt()->willReturn($userSalt);

        $encoder->isPasswordValid($userPassword, $password, $userSalt)->shouldBeCalled()->willReturn(true);

        $tokenFactory->getRefreshToken($user)->willReturn($refreshToken);
        $tokenFactory->create($user, $refreshToken)->willReturn($shopUserToken);

        $eventDispatcher->dispatch(Argument::any(), LoginResolver::EVENT_NAME)->shouldBeCalled();

        $this->__invoke(null, $context);
    }

    function it_throws_an_exception_on_wrong_username(
        UserRepositoryInterface $userRepository
    ): void {
        $context = [
            'args' => [
                'input' => [
                    'username' => 'username',
                    'password' => 'somepass',
                ],
            ],
        ];

        /** @var array $input */
        $input = $context['args']['input'];
        $username = (string) $input['username'];

        $userRepository->findOneBy(['username' => $username])->willReturn(null);

        $this
            ->shouldThrow(\Exception::class)
            ->during('__invoke', [null, $context])
        ;
    }

    function it_throws_an_exception_on_wrong_password(
        UserRepositoryInterface $userRepository,
        EncoderFactoryInterface $encoderFactory,
        PasswordEncoderInterface $encoder,
        ShopUserInterface $user
    ): void {
        $context = [
            'args' => [
                'input' => [
                    'username' => 'username',
                    'password' => 'somepass',
                ],
            ],
        ];

        /** @var array $input */
        $input = $context['args']['input'];
        $username = (string) $input['username'];
        $password = (string) $input['password'];

        $userRepository->findOneBy(['username' => $username])->willReturn($user);
        $encoderFactory->getEncoder($user)->willReturn($encoder);

        $userPassword = 'ENCODED_PASSWORD';
        $userSalt = 'SALT';
        $user->getPassword()->willReturn($userPassword);
        $user->getSalt()->willReturn($userSalt);

        $encoder->isPasswordValid($userPassword, $password, $userSalt)->willReturn(false);

        $this
            ->shouldThrow(\Exception::class)
            ->during('__invoke', [null, $context])
        ;
    }
}
