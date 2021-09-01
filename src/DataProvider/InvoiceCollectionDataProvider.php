<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Webmozart\Assert\Assert;

/** @experimental */
final class InvoiceCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{

    private PaginationExtension $paginationExtension;
    private UserContextInterface $userContext;
    /** @see QueryCollectionExtensionInterface */
    private iterable $collectionExtensions;
    private QueryNameGeneratorInterface $queryNameGenerator;
    private ObjectRepository $invoiceRepository;
    private string $invoiceClass;

    public function __construct(
        EntityManagerInterface $entityManager,
        PaginationExtension $paginationExtension,
        UserContextInterface $userContext,
        QueryNameGeneratorInterface $queryNameGenerator,
        iterable $collectionExtensions,
        string $invoiceClass
    ) {
        $this->paginationExtension = $paginationExtension;
        $this->userContext = $userContext;
        $this->queryNameGenerator = $queryNameGenerator;
        $this->collectionExtensions = $collectionExtensions;
        $this->invoiceClass = $invoiceClass;
        $this->invoiceRepository = $entityManager->getRepository($invoiceClass);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, $this->invoiceClass, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        Assert::keyExists($context, ContextKeys::CHANNEL);

        $user = $this->userContext->getUser();
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles())) {
            return $this->invoiceRepository->findAll();
        }

        if(null === $user){
            throw new AuthenticationException("You are not authenticated to view this resource.");
        }

        $queryBuilder = $this->invoiceRepository->createInvoiceByUserQueryBuilder($user);

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $this->queryNameGenerator, $resourceClass, $operationName, $context);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
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
