<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusVueStorefront2Plugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\TaxonRepositoryInterface;
use BitBag\SyliusVueStorefront2Plugin\DTO\Taxon\TaxonDto;
use BitBag\SyliusVueStorefront2Plugin\DTO\Taxon\TaxonParentDto;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Security\Core\User\UserInterface;
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
        iterable $collectionExtensions,
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
        if ($this->hasAccessToAllTaxa($user)) {
            return $this->taxonRepository->findAll();
        }

        $queryBuilder = $this->taxonRepository->createChildrenByChannelMenuTaxonQueryBuilder(
            $channelMenuTaxon,
        );

        /** @var QueryCollectionExtensionInterface $extension */
        foreach ($this->collectionExtensions as $extension) {
            if ($extension instanceof ContextAwareQueryCollectionExtensionInterface) {
                $extension->applyToCollection($queryBuilder, $this->queryNameGenerator, $resourceClass, $operationName, $context);
            } else {
                $extension->applyToCollection($queryBuilder, $this->queryNameGenerator, $resourceClass, $operationName);
            }

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                return $this->parseResult($extension->getResult($queryBuilder));
            }
        }

        return $this->parseResult(
            $this->paginationExtension->getResult(
                $queryBuilder,
                $resourceClass,
                $operationName,
                $context,
            )
        );
    }

    private function hasAccessToAllTaxa(?UserInterface $user): bool
    {
        /** @psalm-suppress DeprecatedClass */
        return $user !== null && in_array('ROLE_API_ACCESS', $user->getRoles(), true);
    }

    private function parseResult(iterable $result): array
    {
        $data = [];
        foreach ($result as $taxon) {
            $data[] = $this->parseTaxon($taxon);
        }

        return $data;
    }

    private function parseTaxon(TaxonInterface $taxon): TaxonDto
    {
        return new TaxonDto(
            $taxon->getId(),
            $taxon->getName(),
            $taxon->getCode(),
            $taxon->getPosition(),
            $taxon->getSlug(),
            $taxon->getDescription(),
            $taxon->isEnabled(),
            $taxon->getLevel(),
            $taxon->getTranslations(),
            $taxon->getParent() ? new TaxonParentDto($taxon->getParent()) : null,
        );
    }
}
