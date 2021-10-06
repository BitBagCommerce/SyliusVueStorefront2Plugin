<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Resolver\Mutation;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use BitBag\SyliusGraphqlPlugin\Factory\ShopUserTokenFactoryInterface;
use BitBag\SyliusGraphqlPlugin\Model\ShopUserTokenInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class RefreshTokenResolver implements MutationResolverInterface
{
    private EntityManagerInterface $entityManager;

    private UserRepositoryInterface $userRepository;

    private ShopUserTokenFactoryInterface $tokenFactory;

    /** @var EntityRepository<RefreshToken>  */
    private ObjectRepository $refreshTokenRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ShopUserTokenFactoryInterface $tokenFactory,
        UserRepositoryInterface $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->tokenFactory = $tokenFactory;
        $this->userRepository = $userRepository;
        $this->refreshTokenRepository = $entityManager->getRepository(RefreshToken::class);
    }

    public function __invoke($item, array $context): ?ShopUserTokenInterface
    {
        if (!isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];
        $refreshTokenString = (string) $input['refreshToken'];

        $refreshToken = $this->refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenString]);

        if (null === $refreshToken || !$refreshToken->isValid()) {
            throw new AuthenticationException(
                sprintf('Refresh token "%s" is invalid.', $refreshTokenString)
            );
        }

        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneBy(['username' => $refreshToken->getUsername()]);

        $refreshTokenExpirationDate = new \DateTime('+1 month');
        $refreshToken->setValid($refreshTokenExpirationDate);
        $this->entityManager->flush();

        return $this->tokenFactory->create($user, $refreshToken);
    }
}
