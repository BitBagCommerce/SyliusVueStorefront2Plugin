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
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Webmozart\Assert\Assert;

final class RefreshTokenResolver implements MutationResolverInterface
{
    public const EVENT_NAME = 'bitbag.sylius_graphql.mutation_resolver.refresh_token.complete';

    private EntityManagerInterface $entityManager;

    private UserRepositoryInterface $userRepository;

    private ShopUserTokenFactoryInterface $shopUserTokenFactory;

    private EventDispatcherInterface $eventDispatcher;

    /** @var EntityRepository<RefreshToken> */
    private ObjectRepository $refreshTokenRepository;

    private string $refreshTokenLifetime;

    public function __construct(
        EntityManagerInterface $entityManager,
        ShopUserTokenFactoryInterface $tokenFactory,
        UserRepositoryInterface $userRepository,
        EventDispatcherInterface $eventDispatcher,
        string $refreshTokenLifetime,
    ) {
        $this->entityManager = $entityManager;
        $this->shopUserTokenFactory = $tokenFactory;
        $this->userRepository = $userRepository;
        $this->refreshTokenRepository = $entityManager->getRepository(RefreshToken::class);
        $this->eventDispatcher = $eventDispatcher;
        $this->refreshTokenLifetime = $refreshTokenLifetime;
    }

    /**
     * @param ShopUserTokenInterface|object|null $item */
    public function __invoke($item, array $context): ?ShopUserTokenInterface
    {
        if (!isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];
        Assert::keyExists($input, 'refreshToken');
        $refreshTokenString = (string) $input['refreshToken'];

        $refreshToken = $this->refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenString]);

        Assert::notNull($refreshToken);
        $this->validateRefreshToken($refreshToken, $refreshTokenString);

        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneBy(['username' => $refreshToken->getUsername()]);

        $refreshTokenExpirationDate = new \DateTime(sprintf('+%s seconds', $this->refreshTokenLifetime));
        $refreshToken->setValid($refreshTokenExpirationDate);
        $this->entityManager->flush();

        /** @psalm-suppress TooManyArguments */
        $this->eventDispatcher->dispatch(new GenericEvent($user, $input), self::EVENT_NAME);

        return $this->shopUserTokenFactory->create($user, $refreshToken);
    }

    private function validateRefreshToken(?RefreshTokenInterface $refreshToken, string $refreshTokenString): void
    {
        if (null === $refreshToken || !$refreshToken->isValid()) {
            throw new AuthenticationException(
                sprintf('Refresh token "%s" is invalid.', $refreshTokenString),
            );
        }
    }
}
