<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusVueStorefront2Plugin\Integration\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusVueStorefront2Plugin\Filter\ChannelPricingChannelCodeFilter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\ChannelPricingInterface;

final class ChannelPricingChannelCodeFilterTest extends JsonApiTestCase
{
    public function test_it_filters_property_not_support_property(): void
    {
        $context = [
            'filters' => [
                'channel' => 'test',
            ],
        ];

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects(self::never())
            ->method('andWhere')
        ;
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $channelPricingChannelCodeFilter = new ChannelPricingChannelCodeFilter($this->createMock(ManagerRegistry::class));
        $channelPricingChannelCodeFilter->apply(
            $queryBuilder,
            $queryNameGenerator,
            ChannelPricingInterface::class,
            null,
            $context,
        );
    }

    public function test_it_filters_property_not_support_type_of_value(): void
    {
        $context = [
            'filters' => [
                'channelCode' => 1,
            ],
        ];

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects(self::never())
            ->method('andWhere')
        ;
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $channelPricingChannelCodeFilter = new ChannelPricingChannelCodeFilter($this->createMock(ManagerRegistry::class));
        $channelPricingChannelCodeFilter->apply(
            $queryBuilder,
            $queryNameGenerator,
            ChannelPricingInterface::class,
            null,
            $context,
        );
    }

    public function test_it_filters_property_not_support_class(): void
    {
        $context = [
            'filters' => [
                'channelCode' => 'test',
            ],
        ];

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects(self::never())
            ->method('andWhere')
        ;
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $channelPricingChannelCodeFilter = new ChannelPricingChannelCodeFilter($this->createMock(ManagerRegistry::class));
        $channelPricingChannelCodeFilter->apply(
            $queryBuilder,
            $queryNameGenerator,
            ChannelInterface::class,
            null,
            $context,
        );
    }

    public function test_it_filters_property(): void
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $channelPricingRepository = $entityManager->getRepository(ChannelPricing::class);

        $this->loadFixturesFromFile('ChannelPricingChannelCodeFilterTest/channel_pricing_channel_filter.yml');
        $context = [
            'filters' => [
                'channelCode' => 'FASHION_WEB',
            ],
        ];

        $queryBuilder = $channelPricingRepository->createQueryBuilder('o');
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $queryNameGenerator
            ->method('generateParameterName')
            ->willReturn('generateParameter')
        ;

        $channelPricingChannelCodeFilter = new ChannelPricingChannelCodeFilter($this->createMock(ManagerRegistry::class));
        $channelPricingChannelCodeFilter->apply(
            $queryBuilder,
            $queryNameGenerator,
            ChannelPricingInterface::class,
            null,
            $context,
        );

        $result = $queryBuilder->getQuery()->getResult();
        self::assertCount(3, $result);
    }
}
