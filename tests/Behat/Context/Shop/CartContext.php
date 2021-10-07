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
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    private OrderRepositoryInterface $orderRepository;

    private IriConverterInterface $iriConverter;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @When I prepare create cart operation
     */
    public function iPrepareCreateCartOperation(): void
    {
        $expectedData = '
        order {
            id
            tokenValue
        }';

        $operation = $this->client->prepareOperation('shop_postOrder', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare add product to cart operation
     */
    public function iPrepareAddProductToCartOperation(): void
    {
        $expectedData = '
        order {
            items {
                edges{
                    node{
                        variantName
                        id
                        _id
                        productName
                    }
                }
            }
            total
            shippingTotal
        }';

        $operation = $this->client->prepareOperation('shop_add_itemOrder', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare remove product from cart operation
     */
    public function iPrepareRemoveProductFromCartOperation(): void
    {
        $expectedData = '
        order {
            total
            shippingTotal
        }';

        $operation = $this->client->prepareOperation('shop_remove_itemOrder', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare place order operation
     */
    public function iPreparePlaceOrderOperation(): void
    {
        $expectedData = '
        order {
            total
            shippingTotal
        }';

        $operation = $this->client->prepareOperation('shop_completeOrder', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare operation to add order shipping address
     */
    public function iPrepareOperationToAddOrderShippingAddress(): void
    {
        $expectedData = '
        order {
            shippingAddress{
                firstName
                lastName
            }
            shipments{
                edges{
                    node {
                        id
                        _id
                        method {
                            code
                        }
                    }
                }
            }
            shippingTotal
        }';

        $operation = $this->client->prepareOperation('shop_add_shipping_addressOrder', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare operation choose address saved as :addressKey as shipping
     */
    public function iPrepareOperationChoosePredefinedAddressAsShipping(string $addressKey): void
    {
        $expectedData = '
        order {
            shippingAddress{
                firstName
                lastName
            }
            shipments{
                edges{
                    node {
                        id
                        _id
                        method {
                            code
                        }
                    }
                }
            }
            shippingTotal
        }';

        $addressIri = (string) $this->sharedStorage->get($addressKey);
        /** @var AddressInterface $address */
        $address = $this->iriConverter->getItemFromIri($addressIri);

        $operation = $this->client->prepareOperation('shop_add_shipping_addressOrder', $expectedData);
        $shippingAddress = [
            'firstName' => $address->getFirstName(),
            'lastName' => $address->getLastName(),
            'countryCode' => $address->getCountryCode(),
            'city' => $address->getCity(),
            'street' => $address->getStreet(),
            'postcode' => $address->getStreet(),
        ];
        $operation->addVariable('shippingAddress', $shippingAddress);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare operation to add order billing address
     */
    public function iPrepareOperationToAddOrderBillingAddress(): void
    {
        $expectedData = '
        order {

            billingAddress{
                firstName
                lastName
            }
            payments{
                edges{
                    node {
                        id
                        _id
                        method {
                            code
                        }
                    }
                }
            }
            shippingTotal
        }';

        $operation = $this->client->prepareOperation('shop_add_billing_addressOrder', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare operation choose address saved as :addressKey as billing
     */
    public function iPrepareOperationChoosePredefinedAddressAsBilling(string $addressKey): void
    {
        $expectedData = '
        order {
            billingAddress{
                firstName
                lastName
            }
            payments{
                edges{
                    node {
                        id
                        method {
                            code
                        }
                    }
                }
            }
            shippingTotal
        }';

        $addressIri = (string) $this->sharedStorage->get($addressKey);
        /** @var AddressInterface $address */
        $address = $this->iriConverter->getItemFromIri($addressIri);

        $operation = $this->client->prepareOperation('shop_add_billing_addressOrder', $expectedData);
        $shippingAddress = [
            'firstName' => $address->getFirstName(),
            'lastName' => $address->getLastName(),
            'countryCode' => $address->getCountryCode(),
            'city' => $address->getCity(),
            'street' => $address->getStreet(),
            'postcode' => $address->getStreet(),
        ];
        $operation->addVariable('billingAddress', $shippingAddress);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @Then total price for items should equal to :price
     */
    public function totalPriceForItemsShouldEqualTo(int $price)
    {
        $orderTotal = (int) $this->client->getValueAtKey('order.total');
        $shippingTotal = (int) $this->client->getValueAtKey('order.shippingTotal');

        Assert::same($price, ($orderTotal - $shippingTotal));
    }
}
