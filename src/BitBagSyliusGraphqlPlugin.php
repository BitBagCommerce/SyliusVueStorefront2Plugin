<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BitBagSyliusGraphqlPlugin extends Bundle
{
    use SyliusPluginTrait;
}
