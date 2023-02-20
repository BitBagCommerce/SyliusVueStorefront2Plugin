@add_products_to_cart
Feature: Adding multiple products to cart
    In order buy a products
    I need to be able to add multiple products to cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Harness climbing" priced at "$30.00"
        And the store has a product "Rope" priced at "$40.00"

    @graphql
    Scenario: Placing order as guest
        Given There is operation to add products to cart
        And this operation has orderTokenValue
        And this operation has cartItems:
            | productVariant    | quantity  |
            | Harness_climbing  | 2         |
            | Rope              | 3         |

        When I send that GraphQL request

        Then I should receive a JSON response
        And This cart should contain product "Harness climbing" in an amount 2
        And This cart should contain product "Rope" in an amount 3
        And total price for items should equal to 18000

    @graphql
    Scenario: Placing order as customer
        Given there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And I authorize as "aondra@climb.com"
        And There is operation to add products to cart
        And this operation has orderTokenValue for customer identified by an email "aondra@climb.com"
        And this operation has cartItems:
            | productVariant    | quantity  |
            | Harness_climbing  | 1         |
            | Rope              | 4         |

        When I send that GraphQL request as authorised user

        Then I should receive a JSON response
        And This cart should contain product "Harness climbing" in an amount 1
        And This cart should contain product "Rope" in an amount 4
        And total price for items should equal to 19000
