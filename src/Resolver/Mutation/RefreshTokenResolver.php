<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Resolver\Mutation;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use BitBag\SyliusGraphqlPlugin\Factory\ShopUserTokenFactoryInterface;
use BitBag\SyliusGraphqlPlugin\Model\ShopUserToken;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class RefreshTokenResolver implements MutationResolverInterface
{

    private EntityManagerInterface $entityManager;
    private ShopUserTokenFactoryInterface $tokenFactory;
    private string $refreshTokenClass;

    public function __construct(
        EntityManagerInterface $entityManager,
        ShopUserTokenFactoryInterface $tokenFactory,
        string $refreshTokenClass
    ) {
        $this->entityManager = $entityManager;
        $this->tokenFactory = $tokenFactory;
        $this->refreshTokenClass = $refreshTokenClass;
    }

    public function __invoke($item, $context)
    {
        if (!is_array($context) || !isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];
        $refreshTokenString = (string) $input['refreshToken'];

        $refreshTokenRepository = $this->entityManager->getRepository($this->refreshTokenClass);
        /** @var RefreshTokenInterface|null $refreshToken */
        $refreshToken = $refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenString]);

        if (null === $refreshToken || !$refreshToken->isValid()) {
            throw new AuthenticationException(
                sprintf('Refresh token "%s" is invalid.', $refreshTokenString)
            );
        }

        $shopUserRepository = $this->entityManager->getRepository(ShopUser::class);
        /** @var ShopUserInterface $user */
        $user = $shopUserRepository->findOneBy(['username' => $refreshToken->getUsername()]);

        $refreshTokenExpirationDate = new \DateTime('+1 month');
        $refreshToken->setValid($refreshTokenExpirationDate);
        $this->entityManager->flush();

        return $this->tokenFactory->create($user,$refreshToken);

    }
}
