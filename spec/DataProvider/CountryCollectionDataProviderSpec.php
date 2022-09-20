<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\State\Pagination\PaginatorInterface;
use BitBag\SyliusGraphqlPlugin\DataProvider\CountryCollectionDataProvider;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class CountryCollectionDataProviderSpec extends ObjectBehavior
{
    private iterable $collectionExtensions;

    function let(
        EntityRepository $countryRepository,
        QueryNameGeneratorInterface $queryNameGenerator,
        ManagerRegistry $managerRegistry,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
        QueryResultCollectionExtensionInterface $queryResultCollectionExtension
    ): void {
        $pagination = new Pagination($resourceMetadataFactory->getWrappedObject());
        $paginationExtension = new PaginationExtension(
            $managerRegistry->getWrappedObject(),
            $resourceMetadataFactory->getWrappedObject(),
            $pagination,
        );
        $this->collectionExtensions = [
            $queryResultCollectionExtension->getWrappedObject(),
        ];
        $this->beConstructedWith(
            $countryRepository,
            $paginationExtension,
            $queryNameGenerator,
            $this->collectionExtensions
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CountryCollectionDataProvider::class);
    }

    function it_checks_if_supports(CountryInterface $country): void
    {
        $this->supports(get_class($country->getWrappedObject()))->shouldReturn(true);
    }

    function it_gets_a_collection(
        ContextAwareQueryResultCollectionExtensionInterface $paginationExtension,
        ChannelInterface $channel,
        QueryBuilder $queryBuilder,
        EntityRepository $countryRepository,
        PaginatorInterface $paginator,
        QueryNameGeneratorInterface $queryNameGenerator,
        QueryResultCollectionExtensionInterface $queryResultCollectionExtension
    ): void {
        $collectionExtensions = [
            $queryResultCollectionExtension->getWrappedObject(),
        ];
        $this->beConstructedWith(
            $countryRepository,
            $paginationExtension->getWrappedObject(),
            $queryNameGenerator,
            $collectionExtensions
        );

        $context = [
            ContextKeys::CHANNEL => $channel,
        ];
        $resourceClass = CountryInterface::class;
        $operationName = 'operation';

        $countryRepository->createQueryBuilder('o')->willReturn($queryBuilder);

        $queryResultCollectionExtension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName)->shouldBeCalled();
        $queryResultCollectionExtension->supportsResult($resourceClass, $operationName)->shouldBeCalled()->willReturn(false);

        $paginationExtension->getResult($queryBuilder->getWrappedObject(), $resourceClass, $operationName, $context)->willReturn($paginator);

        $this->getCollection($resourceClass, $operationName, $context)->shouldReturn($paginator);
    }

    function it_returns_query_result_collection_iterable(
        ContextAwareQueryResultCollectionExtensionInterface $paginationExtension,
        ChannelInterface $channel,
        QueryBuilder $queryBuilder,
        EntityRepository $countryRepository,
        PaginatorInterface $paginator,
        QueryNameGeneratorInterface $queryNameGenerator,
        QueryResultCollectionExtensionInterface $queryResultCollectionExtension
    ): void {
        $collectionExtensions = [
            $queryResultCollectionExtension->getWrappedObject(),
        ];
        $this->beConstructedWith(
            $countryRepository,
            $paginationExtension->getWrappedObject(),
            $queryNameGenerator,
            $collectionExtensions
        );

        $context = [
            ContextKeys::CHANNEL => $channel,
        ];
        $resourceClass = CountryInterface::class;
        $operationName = 'operation';

        $countryRepository->createQueryBuilder('o')->willReturn($queryBuilder);

        $queryResultCollectionExtension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName)->shouldBeCalled();
        $queryResultCollectionExtension->supportsResult($resourceClass, $operationName)->shouldBeCalled()->willReturn(true);

        $queryResultCollectionExtension->getResult($queryBuilder->getWrappedObject())->willReturn($paginator);

        $this->getCollection($resourceClass, $operationName, $context)->shouldReturn($paginator);
    }
}
