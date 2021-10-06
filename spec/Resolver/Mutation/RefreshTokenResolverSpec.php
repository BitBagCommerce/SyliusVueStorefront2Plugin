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
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class RefreshTokenResolverSpec extends ObjectBehavior
{

    function let(
        EntityManagerInterface $entityManager,
        ShopUserTokenFactoryInterface $tokenFactory,
        UserRepositoryInterface $userRepository
    ): void
    {
        $refreshTokenClass = "Path/To/RefreshTokenClass";
        $this->beConstructedWith($entityManager, $tokenFactory, $userRepository, $refreshTokenClass);
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
        ShopUserTokenInterface $shopUserToken
    ): void
    {
        $refreshTokenClass = "Path/To/RefreshTokenClass";

        $refreshToken = new RefreshToken();
        $refreshToken->setValid(new \DateTime("+1 hour"));
        $refreshToken->setUsername("username");

        $context = [
            "args" => [
                "input" => [
                    "refreshToken" => "token"
                ]
            ]
        ];

        $input = $context['args']['input'];
        $refreshTokenString = (string)$input['refreshToken'];

        $entityManager->getRepository($refreshTokenClass)->willReturn($refreshTokenRepository);

        $refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenString])->willReturn($refreshToken);

        $userRepository->findOneBy(['username' => $refreshToken->getUsername()])->willReturn($user);

        $refreshTokenExpirationDate = new \DateTime('+1 month');
        $refreshToken->setValid($refreshTokenExpirationDate);

        $entityManager->flush()->shouldBeCalledOnce();

        $tokenFactory->create($user, $refreshToken)->willReturn($shopUserToken);
        $this->__invoke(null, $context)->shouldReturn($shopUserToken);
    }

    function it_throws_an_exception_if_token_invalid(
        EntityManagerInterface $entityManager,
        ObjectRepository $refreshTokenRepository,
        RefreshTokenInterface $refreshToken
    ): void
    {
        $refreshTokenClass = "Path/To/RefreshTokenClass";
        $context = [
            "args" => [
                "input" => [
                    "refreshToken" => "token"
                ]
            ]
        ];

        $input = $context['args']['input'];
        $refreshTokenString = (string)$input['refreshToken'];

        $entityManager->getRepository($refreshTokenClass)->willReturn($refreshTokenRepository);

        $refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenString])->willReturn($refreshToken);

        $this
            ->shouldThrow(AuthenticationException::class)
            ->during('__invoke', [null, $context]);
    }
}
