<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\CommandHandler\Account;

use BitBag\SyliusGraphqlPlugin\Command\Account\SendResetPasswordEmail;
use BitBag\SyliusGraphqlPlugin\CommandHandler\Account\SendResetPasswordEmailHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;


final class SendResetPasswordEmailHandlerSpec extends ObjectBehavior
{

    function let(
        SenderInterface $emailSender,
        ChannelContextInterface $channelContext,
        UserRepositoryInterface $userRepository,
        GeneratorInterface $generator
    ): void
    {
        $this->beConstructedWith($emailSender, $channelContext, $userRepository, $generator);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SendResetPasswordEmailHandler::class);
    }

    function it_is_invokable(
        SenderInterface $emailSender,
        ChannelContextInterface $channelContext,
        UserRepositoryInterface $userRepository,
        GeneratorInterface $generator,
        ShopUserInterface $user,
        ChannelInterface $channel,
        CustomerInterface $customer
    ): void
    {
        $resetToken = "hdhgvshjvbwje";

        $command = new SendResetPasswordEmail("en_US", "john.d@gmail.com");

        $userRepository->findOneByEmail($command->email)->willReturn($user);
        $channelContext->getChannel()->willReturn($channel);
        $generator->generate()->willReturn($resetToken);

        $user->setPasswordRequestedAt(Argument::type(\DateTime::class))->shouldBeCalled();
        $user->setPasswordResetToken($resetToken)->shouldBeCalled();

        $emailSender->send(
            Emails::PASSWORD_RESET,
            [$command->email],
            [
                'user' => $user->getWrappedObject(),
                'localeCode' => $command->localeCode,
                'channel' => $channel->getWrappedObject(),
            ]
        )->shouldBeCalled();

        $user->getCustomer()->willReturn($customer);

        $this->__invoke($command);
    }


}