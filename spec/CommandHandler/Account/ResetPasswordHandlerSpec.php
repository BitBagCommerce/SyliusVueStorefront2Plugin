<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\CommandHandler\Account;

use BitBag\SyliusGraphqlPlugin\Command\Account\ResetPassword;
use BitBag\SyliusGraphqlPlugin\CommandHandler\Account\ResetPasswordHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResetPasswordHandlerSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $this->beConstructedWith(
            $userRepository,
            $metadata,
            $passwordUpdater,
            $eventDispatcher
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ResetPasswordHandler::class);
    }

    function it_implements_message_handler_interface(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_is_invokable(
        UserRepositoryInterface $userRepository,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater,
        ShopUserInterface $user,
        CustomerInterface $customer,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $command = new ResetPassword('newS3ret', 'newS3ret', 'token');

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn($user);

        $metadata->getParameter('resetting')->willReturn([
            'token' => [
                'ttl' => 'PT3600S',
            ],
        ]);

        $lifetime = new \DateInterval('PT3600S');
        $user->isPasswordRequestNonExpired($lifetime)->willReturn(true);
        $user->getPasswordResetToken()->willReturn('token');

        $user->setPlainPassword($command->newPassword)->shouldBeCalled();
        $passwordUpdater->updatePassword($user->getWrappedObject())->shouldBeCalled();

        $user->getCustomer()->willReturn($customer);

        $eventDispatcher->dispatch(Argument::any(), ResetPasswordHandler::EVENT_NAME)->shouldBeCalled();

        $this->__invoke($command);
    }

    function it_throws_an_exception_when_user_nor_found(
        UserRepositoryInterface $userRepository
    ): void {
        $command = new ResetPassword('newS3ret', 'newS3ret', 'token');

        $userRepository->findOneBy(['passwordResetToken' => 'token'])->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command]);
    }
}
