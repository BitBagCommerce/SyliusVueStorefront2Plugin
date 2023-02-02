<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusVueStorefront2Plugin\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ContextAwareFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use BitBag\SyliusVueStorefront2Plugin\Filter\ChannelPricingChannelCodeFilter;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;

final class ChannelPricingChannelCodeFilterSpec extends ObjectBehavior
{
    public function let(ManagerRegistry $managerRegistry): void
    {
        $this->beConstructedWith($managerRegistry);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ChannelPricingChannelCodeFilter::class);
        $this->shouldHaveType(ContextAwareFilterInterface::class);
    }

    public function it_filters_property(
        QueryBuilder $queryBuilder,
        Expr $expr,
        Comparison $comparison,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $property = 'channelCode';
        $value = 'FASHION_WEB';
        $context = [
            'filters' => [
                $property => $value,
            ],
        ];

        $queryNameGenerator->generateParameterName($property)->willReturn('parameterName');

        $expr->like('alias.channelCode', ':parameterName')->willReturn($comparison);

        $queryBuilder->getRootAliases()->willReturn(['alias']);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->andWhere($comparison)->shouldBeCalled();
        $queryBuilder->setParameter('parameterName', $value)->shouldBeCalled();

        $this->apply(
            $queryBuilder,
            $queryNameGenerator,
            ChannelPricingInterface::class,
            null,
            $context,
        );
    }

    public function it_filters_property_not_support_property(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $context = [
            'filters' => [
                'channel' => 'test',
            ],
        ];

        $queryNameGenerator->generateParameterName(Argument::any())->shouldNotBeCalled();

        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->apply(
            $queryBuilder,
            $queryNameGenerator,
            ChannelPricingInterface::class,
            null,
            $context,
        );
    }

    public function it_filters_property_not_support_type_of_value(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $context = [
            'filters' => [
                'channelCode' => 1,
            ],
        ];

        $queryNameGenerator->generateParameterName(Argument::any())->shouldNotBeCalled();

        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->apply(
            $queryBuilder,
            $queryNameGenerator,
            ChannelPricingInterface::class,
            null,
            $context,
        );
    }

    public function it_filters_property_not_support_class(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $context = [
            'filters' => [
                'channelCode' => 'test',
            ],
        ];

        $queryNameGenerator->generateParameterName(Argument::any())->shouldNotBeCalled();

        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->apply(
            $queryBuilder,
            $queryNameGenerator,
            ChannelInterface::class,
            null,
            $context,
        );
    }

    public function it_gets_description(): void
    {
        $this->getDescription('resourceClass')->shouldReturn(
            [
                'channelCode' => [
                    'type' => 'string',
                    'required' => true,
                    'property' => null,
                    'swagger' => [
                        'name' => 'Channel pricing filter',
                        'description' => 'Get a collection of channel pricing for channelCode',
                    ],
                ],
            ],
        );
    }
}
