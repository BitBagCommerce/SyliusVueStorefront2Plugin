<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\Resolver\Mutation;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use BitBag\SyliusVueStorefront2Plugin\Factory\ShopUserTokenFactoryInterface;
use BitBag\SyliusVueStorefront2Plugin\Model\ShopUserTokenInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Webmozart\Assert\Assert;

final class LoginResolver implements MutationResolverInterface
{
    public const EVENT_NAME = 'bitbag.sylius_vue_storefront2.mutation_resolver.login.complete';

    private EncoderFactoryInterface $encoderFactory;

    private EntityManagerInterface $entityManager;

    private UserRepositoryInterface $userRepository;

    private OrderRepositoryInterface $orderRepository;

    private ShopUserTokenFactoryInterface $tokenFactory;

    private EventDispatcherInterface $eventDispatcher;

    private ChannelContextInterface $channelContext;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepositoryInterface $userRepository,
        OrderRepositoryInterface $orderRepository,
        EncoderFactoryInterface $encoderFactory,
        ShopUserTokenFactoryInterface $tokenFactory,
        EventDispatcherInterface $eventDispatcher,
        ChannelContextInterface $channelContext,
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->encoderFactory = $encoderFactory;
        $this->tokenFactory = $tokenFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->channelContext = $channelContext;
    }

    /**
     * @param ShopUserTokenInterface|object|null $item
     *
     * @psalm-suppress DeprecatedClass
     */
    public function __invoke($item, array $context): ?ShopUserTokenInterface
    {
        if (!isset($context['args']['input'])) {
            return null;
        }

        /** @var array $input */
        $input = $context['args']['input'];

        $username = (string) $input['username'];
        $password = (string) $input['password'];

        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['username' => $username]);

        Assert::notNull($user, 'Wrong credentials.');

        /** @phpstan-ignore-next-line  */
        $encoder = $this->encoderFactory->getEncoder($user);

        $userPassword = $user->getPassword();
        $userSalt = $user->getSalt();
        Assert::notNull($userPassword);
        Assert::notNull($userSalt);

        /** @var ChannelInterface $currentChannel */
        $currentChannel = $this->channelContext->getChannel();

        if ($currentChannel->isAccountVerificationRequired() && $user->isVerified() === false) {
            throw new \Exception('User verification required.');
        }
        if ($encoder->isPasswordValid($userPassword, $password, $userSalt)) {
            $refreshToken = $this->tokenFactory->getRefreshToken($user);
            $shopUserToken = $this->tokenFactory->create($user, $refreshToken);
            $this->applyOrder($input, $user);

            /** @psalm-suppress TooManyArguments */
            $this->eventDispatcher->dispatch(new GenericEvent($shopUserToken, $input), self::EVENT_NAME);

            return $shopUserToken;
        }

        throw new \Exception('Wrong credentials.');
    }

    private function applyOrder(array $input, ShopUserInterface $user): void
    {
        if (!array_key_exists('orderTokenValue', $input)) {
            return;
        }
        $tokenValue = (string) $input['orderTokenValue'];

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findCartByTokenValue($tokenValue);

        if ($order === null) {
            return;
        }

        $order->setCustomer($user->getCustomer());

        $this->entityManager->flush();
    }
}
