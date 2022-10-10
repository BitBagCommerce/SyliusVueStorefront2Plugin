<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin;

use BitBag\SyliusVueStorefront2Plugin\DependencyInjection\BitBagSyliusVueStorefront2Extension;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Webmozart\Assert\Assert;

final class BitBagSyliusVueStorefront2Plugin extends Bundle
{
    use SyliusPluginTrait;

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!$this->containerExtension instanceof BitBagSyliusVueStorefront2Extension) {
            $extension = $this->createContainerExtension();
            Assert::notNull($extension);
            $this->containerExtension = $extension;
        }

        return $this->containerExtension;
    }
}
