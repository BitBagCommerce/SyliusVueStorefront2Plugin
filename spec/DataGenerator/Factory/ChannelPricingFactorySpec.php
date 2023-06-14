<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory\ChannelPricingFactory;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ChannelPricingFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $channelPricingFactory): void
    {
        $this->beConstructedWith($channelPricingFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ChannelPricingFactory::class);
    }

    public function it_creates(
        FactoryInterface $channelPricingFactory,
        ChannelInterface $channel,
    ): void {
        $price = 122333;
        $channelCode = 'TST';

        $channelPricing = new ChannelPricing();

        $channelPricingFactory->createNew()->willReturn($channelPricing);
        $channelPricing->setPrice($price);
        $channel->getCode()->willReturn($channelCode);
        $channelPricing->setChannelCode($channelCode);

        $this->create($price, $channel);
    }
}
