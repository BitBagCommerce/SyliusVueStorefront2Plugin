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
use BitBag\SyliusGraphqlPlugin\Resolver\Mutation\RefreshTokenResolver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class RefreshTokenResolverSpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $entityManager,
        ShopUserTokenFactoryInterface $tokenFactory,
        UserRepositoryInterface $userRepository,
        ObjectRepository $refreshTokenRepository,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $entityManager->getRepository(RefreshToken::class)->willReturn($refreshTokenRepository);
        $lifespan = '2592000';
        $this->beConstructedWith(
            $entityManager,
            $tokenFactory,
            $userRepository,
            $eventDispatcher,
            $lifespan
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RefreshTokenResolver::class);
    }

    function it_is_invokable(
        EntityManagerInterface $entityManager,
        ShopUserTokenFactoryInterface $tokenFactory,
        UserRepositoryInterface $userRepository,
        ObjectRepository $refreshTokenRepository,
        ShopUserInterface $user,
        ShopUserTokenInterface $shopUserToken,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $refreshTokenClass = 'Path/To/RefreshTokenClass';

        $refreshToken = new RefreshToken();
        $refreshToken->setValid(new \DateTime('+1 hour'));
        $refreshToken->setUsername('username');

        $context = [
            'args' => [
                'input' => [
                    'refreshToken' => 'token',
                ],
            ],
        ];

        $input = $context['args']['input'];
        $refreshTokenString = (string) $input['refreshToken'];

        $entityManager->getRepository($refreshTokenClass)->willReturn($refreshTokenRepository);

        $refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenString])->willReturn($refreshToken);

        $userRepository->findOneBy(['username' => $refreshToken->getUsername()])->willReturn($user);

        $refreshTokenExpirationDate = new \DateTime('+1 month');
        $refreshToken->setValid($refreshTokenExpirationDate);

        $entityManager->flush()->shouldBeCalled();

        $tokenFactory->create($user, $refreshToken)->willReturn($shopUserToken);

        $eventDispatcher->dispatch(Argument::any(), RefreshTokenResolver::EVENT_NAME)->shouldBeCalled();

        $this->__invoke(null, $context)->shouldReturn($shopUserToken);
    }

    function it_throws_an_exception_if_token_is_invalid(
        EntityManagerInterface $entityManager,
        ObjectRepository $refreshTokenRepository,
        RefreshTokenInterface $refreshToken
    ): void {
        $refreshTokenClass = 'Path/To/RefreshTokenClass';
        $context = [
            'args' => [
                'input' => [
                    'refreshToken' => 'token',
                ],
            ],
        ];

        $input = $context['args']['input'];
        $refreshTokenString = (string) $input['refreshToken'];

        $entityManager->getRepository($refreshTokenClass)->willReturn($refreshTokenRepository);

        $refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenString])->willReturn($refreshToken);

        $this
            ->shouldThrow(AuthenticationException::class)
            ->during('__invoke', [null, $context]);
    }

    function it_throws_an_exception_when_token_is_not_found(
        EntityManagerInterface $entityManager,
        ObjectRepository $refreshTokenRepository,
        RefreshTokenInterface $refreshToken
    ): void {
        $refreshTokenClass = 'Path/To/RefreshTokenClass';
        $context = [
            'args' => [
                'input' => [
                    'refreshToken' => 'token',
                ],
            ],
        ];

        $input = $context['args']['input'];
        $refreshTokenString = (string) $input['refreshToken'];

        $entityManager->getRepository($refreshTokenClass)->willReturn($refreshTokenRepository);

        $refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenString])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [null, $context]);
    }
}
