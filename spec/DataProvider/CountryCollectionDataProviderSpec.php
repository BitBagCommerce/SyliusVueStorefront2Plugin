<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use BitBag\SyliusGraphqlPlugin\DataProvider\CountryCollectionDataProvider;
use Doctrine\Persistence\ManagerRegistry;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\CountryInterface;

final class CountryCollectionDataProviderSpec extends ObjectBehavior
{
    function let(
        EntityRepository $countryRepository,
        QueryNameGeneratorInterface $queryNameGenerator,
        ManagerRegistry $managerRegistry,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
        QueryResultCollectionExtensionInterface $queryResultCollectionExtension,
    ): void {
        $pagination = new Pagination($resourceMetadataFactory->getWrappedObject());
        $paginationExtension = new PaginationExtension(
            $managerRegistry->getWrappedObject(),
            $resourceMetadataFactory->getWrappedObject(),
            $pagination,
        );
        $collectionExtensions = [
            $queryResultCollectionExtension->getWrappedObject(),
        ];
        $this->beConstructedWith(
            $countryRepository,
            $paginationExtension,
            $queryNameGenerator,
            $collectionExtensions,
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
}
