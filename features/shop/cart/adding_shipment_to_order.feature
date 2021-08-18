@shopping_cart
Feature: Selecting a shipment for the order
    In order to obtain my order
    As a Visitor
    I want to be able to select shipment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "abc" shipping method with "1000" fee per unit

    @graphql
    Scenario: Adding a simple product to the cart
        Given the store has a product "T-shirt banana" priced at "$12.50"
        When I send the following GraphQL request:
        """
        mutation createCart {
            shop_postOrder(input: {localeCode: "en_US"}) {
                order {
                    tokenValue
                }
            }
        }
        """
        Then I save value at key "order.tokenValue" from last response as "orderId".
        When I have the following GraphQL request:
        """
        mutation addItemToCart ($input: shop_add_itemOrderInput!) {
            shop_add_itemOrder(input: $input) {
                order{ total }
            }
        }
        """
        And I prepare the variables for GraphQL request with saved data:
        """
        {
            "input": {
                "id": "/api/v2/shop/orders/{orderId}",
                "productVariant": "/api/v2/shop/product-variants/T_SHIRT_BANANA",
                "quantity": 2
          }
        }
        """

        Then I have the following GraphQL request:
        """
        mutation addShipping ($selectShippingInput: shop_select_shipping_methodOrderInput!) {
            shop_select_shipping_methodOrder(input: $selectShippingInput){
                order{
                    shippingTotal
                }
            }
        }
        """
        And I prepare the variables for GraphQL request with saved data:
        """
        {
            "selectShippingInput":  {
                "id": "/api/v2/shop/orders/{orderId}",
                "shippingMethodCode": "abc"
            }
        }
        """
        Then I should see following response:
        """
        {
            "data": {
                "order": {
                    "shippingTotal": 2000
                }
            }
        }
        """
