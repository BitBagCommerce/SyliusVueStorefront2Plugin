@adding_promotion
Feature: Order promotions integrity
    In order to have valid promotions applied on my order
    As a Customer
    I want to be able to supply a promotion coupon

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows paying offline
        And the store ships everywhere for free
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "christmas"
        And I am a logged in customer

    @graphql
    Scenario: Adding and removing simple discount promotion coupon
        Given this promotion gives "$10.00" discount to every order
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
                "productVariant": "/api/v2/shop/product-variants/PHP_T_SHIRT",
                "quantity": 2
          }
        }
        """
        Then I have the following GraphQL request:
        """
        mutation applyCoupon ($applyCouponInput: shop_apply_couponOrderInput!) {
            shop_apply_couponOrder(input:$applyCouponInput){
                order{
                    total
                }
            }
        }
        """
        And I prepare the variables for GraphQL request with saved data:
        """
        {
            "applyCouponInput": {
                "orderTokenValue": "{orderId}",
                "couponCode":  "christmas"
            }
        }
        """
        Then I should see following response:
        """
        {
            "data": {
                "shop_apply_couponOrder": {
                    "order": {
                        "total": 19000
                    }
                }
            }
        }
        """
