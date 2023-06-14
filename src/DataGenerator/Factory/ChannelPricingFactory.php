<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataGenerator\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ChannelPricingFactory implements ChannelPricingFactoryInterface
{
    private FactoryInterface $channelPricingFactory;

    public function __construct(FactoryInterface $channelPricingFactory)
    {
        $this->channelPricingFactory = $channelPricingFactory;
    }

    public function create(
        int $price,
        ChannelInterface $channel,
    ): ChannelPricingInterface {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($price);
        $channelPricing->setChannelCode($channel->getCode());

        return $channelPricing;
    }
}
