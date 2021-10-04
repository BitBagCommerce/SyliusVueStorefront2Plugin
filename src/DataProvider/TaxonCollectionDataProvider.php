<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use BitBag\SyliusGraphqlPlugin\Doctrine\Repository\TaxonRepositoryInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class TaxonCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private TaxonRepositoryInterface $taxonRepository;

    private PaginationExtension $paginationExtension;

    private UserContextInterface $userContext;

    /** @see QueryCollectionExtensionInterface */
    private iterable $collectionExtensions;

    private QueryNameGeneratorInterface $queryNameGenerator;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        PaginationExtension $paginationExtension,
        UserContextInterface $userContext,
        QueryNameGeneratorInterface $queryNameGenerator,
        iterable $collectionExtensions
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->paginationExtension = $paginationExtension;
        $this->userContext = $userContext;
        $this->queryNameGenerator = $queryNameGenerator;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, TaxonInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channelContext = $context[ContextKeys::CHANNEL];
        Assert::isInstanceOf($channelContext, ChannelInterface::class);
        $channelMenuTaxon = $channelContext->getMenuTaxon();

        $user = $this->userContext->getUser();
        /** @psalm-suppress DeprecatedClass */
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->taxonRepository->findAll();
        }

        $queryBuilder = $this->taxonRepository->createChildrenByChannelMenuTaxonQueryBuilder(
            $channelMenuTaxon
        );

        /** @var QueryCollectionExtensionInterface $extension */
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $this->queryNameGenerator, $resourceClass, $operationName);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                return $extension->getResult($queryBuilder);
            }
        }

        return $this->paginationExtension->getResult(
            $queryBuilder,
            $resourceClass,
            $operationName,
            $context
        );
    }
}
