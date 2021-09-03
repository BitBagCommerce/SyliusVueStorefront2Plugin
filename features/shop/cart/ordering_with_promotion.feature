@shopping_cart
Feature: Submitting single order
    In order to submit the order
    As a Visitor
    I want to be able to provide all its data

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt PHP" priced at "$10.00"
        And the store has a product "Tank-top" priced at "$15.00"
        And the store allows paying offline

    @graphql
    Scenario: Adding a simple product to the cart



#
#        Given the store has a product "T-shirt banana" priced at "$12.50"
#        When I send the following GraphQL request:
#        """
#        mutation createCart {
#            shop_postOrder(input: {localeCode: "en_US"}) {
#                order {
#                    tokenValue
#                }
#            }
#        }
#        """
#        Then I save value at key "order.tokenValue" from last response as "orderId".
#        When I have the following GraphQL request:
#        """
#        mutation addItemToCart ($input: shop_add_itemOrderInput!) {
#            shop_add_itemOrder(input: $input) {
#                order{ total }
#            }
#        }
#        """
#        And I prepare the variables for GraphQL request with saved data:
#        """
#        {
#            "input": {
#                "id": "/api/v2/shop/orders/{orderId}",
#                "productVariant": "/api/v2/shop/product-variants/T_SHIRT_BANANA",
#                "quantity": 1
#          }
#        }
#        """
#
#        Then I have the following GraphQL request:
#        """
#         mutation addPaymentMethod ($selectPaymentInput: shop_select_payment_methodOrderInput!) {
#          shop_select_payment_methodOrder(input: $selectPaymentInput){
#            order{
#              payments{
#                edges {
#                  node{
#                    id
#                  }
#                }
#              }
#            }
#          }
#        }
#        """
#        And I prepare the variables for GraphQL request with saved data:
#        """
#        {
#          "selectPaymentInput": {
#            "id": "/api/v2/shop/orders/{orderId}",
#            "paymentMethodCode": "PM_Offline"
#          }
#        }
#        """
#
#        Then I should see following response:
#        """
#        {
#            "data": {
#                "order": {
#                    "payments": {
#                        "edges": []
#                    }
#                }
#            }
#        }
#        """
