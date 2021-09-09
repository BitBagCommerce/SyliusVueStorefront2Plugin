<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Command\Account;

/** @experimental */
class SendResetPasswordEmail
{
//    public string $channelCode;

    public string $localeCode;

    public string $email;

    public function __construct(
//        string $channelCode,
        string $localeCode,
        string $email
    ) {
//        $this->channelCode = $channelCode;
        $this->localeCode = $localeCode;
        $this->email = $email;
    }
}
