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
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;


final class ResetPasswordHandlerSpec extends ObjectBehavior
{

    function let(
        UserRepositoryInterface $userRepository,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater
    ): void
    {
        $this->beConstructedWith($userRepository, $metadata, $passwordUpdater);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ResetPasswordHandler::class);
    }

    function it_implements_message_handler_interface(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    /**
     * @throws \Exception
     */
    function it_changes_customer_password(
        UserRepositoryInterface $userRepository,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater,
        ShopUserInterface $user,
        CustomerInterface $customer
    ): void
    {
        $command = new ResetPassword("newS3ret", "newS3ret", "token");

        $userRepository->findOneBy(['passwordResetToken' => "token"])->willReturn($user);

        $metadata->getParameter('resetting')->willReturn([
            "token" => [
                "ttl" => "PT3600S"
            ]
        ]);

        $lifetime = new \DateInterval('PT3600S');
        $user->isPasswordRequestNonExpired($lifetime)->willReturn(true);
        $user->getPasswordResetToken()->willReturn("token");

        $user->setPlainPassword($command->newPassword)->shouldBeCalled();
        $passwordUpdater->updatePassword($user->getWrappedObject())->shouldBeCalledOnce();

        $user->getCustomer()->willReturn($customer);

        $this->__invoke($command);
    }
}
