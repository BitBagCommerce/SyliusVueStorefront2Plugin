<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\ContextModel\DataGeneratorCommandContextInterface;
use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\Context\DataGeneratorCommandContextFactory;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DataGeneratorCommandContextFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DataGeneratorCommandContextFactory::class);
    }

    function it_returns_data_generator_command_context(
        SymfonyStyle $io,
        ChannelInterface $channel,
        DataGeneratorCommandContextInterface $commandContext,
    ): void {
        $defaultInt = 70;

        $this
            ->fromInput(
                $io->getWrappedObject(),
                $channel->getWrappedObject(),
                $defaultInt,
                $defaultInt,
                $defaultInt,
                $defaultInt,
                $defaultInt,
                $defaultInt,
                $defaultInt,
                $defaultInt,
            )
            ->shouldReturnAnInstanceOf(DataGeneratorCommandContextInterface::class);
    }
}
