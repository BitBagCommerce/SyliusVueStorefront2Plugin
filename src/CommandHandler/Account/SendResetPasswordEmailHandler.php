<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Account;

use BitBag\SyliusGraphqlPlugin\Command\Account\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class SendResetPasswordEmailHandler implements MessageHandlerInterface
{
    public const EVENT_NAME = 'bitbag_sylius_graphql.send_reset_password_email.complete';

    private SenderInterface $emailSender;

    private ChannelContextInterface $channelContext;

    private UserRepositoryInterface $userRepository;

    private GeneratorInterface $generator;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        SenderInterface $emailSender,
        ChannelContextInterface $channelContext,
        UserRepositoryInterface $userRepository,
        GeneratorInterface $generator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->emailSender = $emailSender;
        $this->channelContext = $channelContext;
        $this->userRepository = $userRepository;
        $this->generator = $generator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(SendResetPasswordEmail $command): CustomerInterface
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneByEmail($command->email);
        Assert::notNull($user, 'User with following email could not be found.');

        $channel = $this->channelContext->getChannel();

        $user->setPasswordResetToken($this->generator->generate());
        $user->setPasswordRequestedAt(new \DateTime());

        $this->emailSender->send(
            Emails::PASSWORD_RESET,
            [$command->email],
            [
                'user' => $user,
                'localeCode' => $command->localeCode,
                'channel' => $channel,
            ]
        );

        $customer = $user->getCustomer();
        Assert::notNull($customer);

        $this->eventDispatcher->dispatch(new GenericEvent($user, [$command]), self::EVENT_NAME);

        return $customer;
    }
}
