<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CommandHandler\Account;

use BitBag\SyliusGraphqlPlugin\Command\Account\ResetPassword;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ResetPasswordHandler implements MessageHandlerInterface
{
    private UserRepositoryInterface $userRepository;

    private MetadataInterface $metadata;

    private PasswordUpdaterInterface $passwordUpdater;

    public function __construct(
        UserRepositoryInterface $userRepository,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater
    )
    {
        $this->userRepository = $userRepository;
        $this->metadata = $metadata;
        $this->passwordUpdater = $passwordUpdater;
    }

    /**
     * @param ResetPassword $command
     * @return CustomerInterface
     * @throws \Exception
     */
    public function __invoke(ResetPassword $command): CustomerInterface
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $command->resetPasswordToken]);

        Assert::notNull($user);

        $resetting = $this->metadata->getParameter('resetting');
        Assert::isArray($resetting);
        Assert::keyExists($resetting,"token");
        Assert::isArray($resetting["token"]);
        $lifetime = new \DateInterval((string)$resetting['token']['ttl']);

        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            throw new \InvalidArgumentException('Password reset token has expired');
        }

        if ($command->resetPasswordToken !== $user->getPasswordResetToken()) {
            throw new \InvalidArgumentException('Password reset token does not match.');
        }

        $user->setPlainPassword($command->newPassword);

        $this->passwordUpdater->updatePassword($user);

        $customer = $user->getCustomer();
        Assert::notNull($customer);
        return $customer;
    }
}
