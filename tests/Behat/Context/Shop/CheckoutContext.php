<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusGraphqlPlugin\Behat\Context\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClient;
use Tests\BitBag\SyliusGraphqlPlugin\Behat\Client\GraphqlClientInterface;

final class CheckoutContext implements Context
{
    private GraphqlClientInterface $client;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        GraphqlClientInterface $client,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I prepare operation to select shipping method
     */
    public function iPrepareOperationToSelectShippingMethod(): void
    {
        $expectedData = '
        order {
            shipments{
                edges{
                    node {
                        method {
                            code
                        }
                    }
                }
            }
            shippingTotal
        }';

        $operation = $this->client->prepareOperation('shop_select_shipping_methodOrder', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare operation to select payment method
     */
    public function iPrepareOperationToSelectPaymentMethod(): void
    {
        $expectedData = '
        order {
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
            shipments{
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
            total

        }';

        $operation = $this->client->prepareOperation('shop_select_payment_methodOrder', $expectedData);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare operation to submit order
     * @When I prepare operation to submit order with note :note
     */
    public function iPrepareOperationToSubmitOrder(?string $note = ''): void
    {
        $expectedData = '
        order {
            id
            _id
            payments{
                edges{
                    node {
                        method {
                            code
                        }
                    }
                }
            }
            billingAddress{
                firstName
                lastName
                countryCode
                city
                street
                postcode
            }
            shippingAddress{
                firstName
                lastName
                countryCode
                city
                street
                postcode
            }
            currencyCode
            checkoutState
            paymentState
            shippingState
            shippingTotal
            taxTotal
            total
        }';

        $operation = $this->client->prepareOperation('shop_completeOrder', $expectedData);
        $operation->addVariable('notes', $note);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }

    /**
     * @When I prepare operation to add promotion coupon :coupon
     */
    public function iPrepareOperationToAddPromotionCouponOrder(string $coupon): void
    {
        $expectedData = '
        order {
            orderPromotionTotal
            promotionCoupon{
                code
            }
            taxTotal
            total
        }';

        $operation = $this->client->prepareOperation('shop_apply_couponOrder', $expectedData);
        $operation->addVariable('couponCode', $coupon);
        $this->sharedStorage->set(GraphqlClient::GRAPHQL_OPERATION, $operation);
    }
}
