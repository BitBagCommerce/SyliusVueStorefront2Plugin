<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Context\Shop;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Model\OperationRequestInterface;

final class ProductContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    private ProductRepositoryInterface $productRepository;

    private IriConverterInterface $iriConverter;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        IriConverterInterface $iriConverter,
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @When I prepare query to fetch all products
     */
    public function iPrepareFetchProductsQuery(): void
    {
        $expectedData = '
        collection {
            _id
            sku: code
            name
            slug
            mainTaxon {
                code
            }
            images {
                collection {
                    id
                    path
                }
            }
            variants {
                collection {
                    id
                    code
                }
            }
            productTaxons {
                collection {
                    id
                }
            }
            attributes {
                collection {
                    stringValue
                    code
                    name
                }
            }
            shortDescription
            description
            metaKeywords
            metaDescription
            enabled
        }';

        $operation = $this->client->prepareQuery('products', $expectedData);
        $operation->setOperationType(OperationRequestInterface::OPERATION_QUERY);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }
}
