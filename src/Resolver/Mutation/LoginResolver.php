<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Resolver\Mutation;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use BitBag\SyliusGraphqlPlugin\Model\ShopUserToken;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

final class LoginResolver implements MutationResolverInterface
{
    private EncoderFactoryInterface $encoderFactory;

    private EntityManagerInterface $entityManager;

    private JWTTokenManagerInterface $jwtManager;

    private RefreshTokenManagerInterface $refreshJwtManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        EncoderFactoryInterface $encoderFactory,
        RefreshTokenManagerInterface $refreshJwtManager
    ) {
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->encoderFactory = $encoderFactory;
        $this->refreshJwtManager = $refreshJwtManager;
    }

    public function __invoke($item, $context)
    {
        if (!is_array($context) || !isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];

        $username = (string) $input['username'];
        $password = (string) $input['password'];

        $shopUserRepository = $this->entityManager->getRepository(ShopUser::class);

        /** @var ShopUserInterface $user */
        $user = $shopUserRepository->findOneBy(['username' => $username]);

        $encoder = $this->encoderFactory->getEncoder($user);

        if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
            $token = $this->jwtManager->create($user);
            $refreshToken = $this->refreshJwtManager->create();

            $shopUserToken = new ShopUserToken();
            $shopUserToken->setId($user->getId());
            $shopUserToken->setToken($token);
            $shopUserToken->setRefreshToken($refreshToken->getRefreshToken());
            $shopUserToken->setUser($user);

            $this->applyOrder($input, $user);

            return $shopUserToken;
        }

        return null;
    }

    public function applyOrder(array $input, ShopUserInterface $user): void
    {
        if (!array_key_exists('orderTokenValue', $input)) {
            return;
        }
        $tokenValue = (string) $input['orderTokenValue'];
        $orderRepository = $this->entityManager->getRepository(Order::class);

        /** @var OrderInterface|null $order */
        $order = $orderRepository->findCartByTokenValue($tokenValue);

        if ($order === null) {
            return;
        }

        $order->setCustomer($user->getCustomer());

        $this->entityManager->flush();

    }
}
