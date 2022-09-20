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
use BitBag\SyliusGraphqlPlugin\DataProvider\TaxonCollectionDataProvider;
use BitBag\SyliusGraphqlPlugin\Doctrine\Repository\TaxonRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class TaxonCollectionDataProviderSpec extends ObjectBehavior
{
    function let(
        TaxonRepositoryInterface $taxonRepository,
        UserContextInterface $userContext,
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
        $collectionExtensions = [
            $queryResultCollectionExtension->getWrappedObject(),
        ];
        $this->beConstructedWith(
            $taxonRepository,
            $paginationExtension,
            $userContext,
            $queryNameGenerator,
            $collectionExtensions,
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(TaxonCollectionDataProvider::class);
    }

    function it_checks_if_supports(TaxonInterface $taxon): void
    {
        $this->supports(get_class($taxon->getWrappedObject()))->shouldReturn(true);
    }

    function it_gets_all_collection_for_user_with_api_access(
        ChannelInterface $channel,
        TaxonInterface $channelMenuTaxon,
        UserContextInterface $userContext,
        ShopUserInterface $user,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $context = [
            ContextKeys::CHANNEL => $channel,
        ];
        $channelContext = $context[ContextKeys::CHANNEL];
        $channelContext->getMenuTaxon()->willReturn($channelMenuTaxon);

        $userContext->getUser()->willReturn($user);
        $roles = ['ROLE_API_ACCESS'];
        $user->getRoles()->willReturn($roles);
        $taxonRepository->findAll()->shouldBeCalled();

        $this->getCollection('class', 'operation', $context);
    }
}
